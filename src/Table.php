<?php

namespace Parfumix\TableManager;

use Parfumix\FormBuilder\Form;
use Parfumix\FormBuilder;
use Flysap\Support\Traits\ElementAttributes;
use Parfumix\TableManager\Traits\Columns;
use Parfumix\TableManager\Traits\Rows;
use Flysap\Support;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class Table {

    use ElementAttributes;

    use Columns, Rows;

    /**
     * @var
     */
    protected $url;

    /**
     * @var mixed
     */
    protected $configurations;

    /**
     * @var
     */
    private $driver;

    /**
     * @var int
     */
    protected $perPage = 10;

    public function __construct(DriverAble $driver) {
        $this->configurations = config('table-manager');

        $this->setDriver(
            $driver
        );
    }


    /**
     * Set Driver .
     *
     * @param DriverAble $driver
     * @return $this
     */
    public function setDriver(DriverAble $driver) {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Get driver .
     * @return mixed
     * @throws TableException
     * @internal param $driver
     */
    public function getDriver() {
        if(! $this->hasDriver())
            throw new TableException(_('Set driver.!'));

        return $this->driver;
    }

    /**
     * Has driver ?
     *
     * @return bool
     */
    public function hasDriver() {
        return isset($this->driver);
    }


    /**
     * Set store url .
     *
     * @param $url
     * @return $this
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get store url .
     *
     * @return mixed
     */
    public function getUrl() {
        return $this->url;
    }


    /**
     * Filter decorator .
     *
     * @param callable $filter
     * @return $this
     */
    public function filter(\Closure $filter) {
        $this->getDriver()
            ->filter($filter);

        return $this;
    }


    /**
     * Set perPage .
     *
     * @param $perPage
     * @return $this
     */
    public function setPerPage($perPage) {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Get per page.
     *
     * @return int
     */
    public function getPerPage() {
         return isset($this->configurations['perPage']) ? $this->configurations['perPage'] :$this->perPage;
    }

    /**
     * Paginator decorator .
     *
     * @param null $perPage
     * @param array $appends
     * @return mixed
     * @throws TableException
     */
    public function paginate($perPage = null, array $appends = array()) {
        $perPage = isset($perPage) ? $perPage : $this->getPerPage();

        $data = $this->getDriver()
            ->getData($perPage);

        $rows  = $data['rows'];
        $total = $data['total'];

        $paginator = (new LengthAwarePaginator(
            $rows, $total, $perPage
        ));

        $paginator->setPath(
            Paginator::resolveCurrentPath()
        );

        $paginator->appends($appends);

        return $paginator;
    }


    /**
     * Render table .
     *
     * @return string
     * @throws TableException
     */
    public function render() {
        $html = '<table ';
        $html .= $this->renderAttributes(['class', 'id']);
        $html .= '>';

        $html .= '<thead>';
        $html .= '<tr>';

        $data = $this->getDriver()->getData($this->getPerPage());
        $this->addColumns($data['columns']);
        $this->addRows($data['rows'], $this->getColumns());

        $columns = $this->getColumns();
        array_walk($columns, function(Column $column) use(& $html) {
            $html .= $column->render();
        });
        $html .= '</tr>';
        $html .= '</thead>';

        $html .= '<tbody>';
        $rows = $this->getRows();
        array_walk($rows, function(Row $row) use(& $html) {
            $html .= $row->render();
        });

        $html .= '</tbody>';

        $html .= '</table>';

        $html .= $this->getJs();

        /** If there is sortable connected than start . */
        if( $this->hasAttribute('sortable') && $this->getAttribute('sortable') )
            $html .= $this->getSortableJs(
                $this->getAttribute('sortable')
            );

        return $html;
    }

    /**
     * Get filter form ..
     *
     * @param null $request
     * @return Form
     * @throws FormBuilder\ElementException
     */
    public function renderFilter($request = null) {
        $fields = $this->getDriver()
            ->filterFields();

        if(! $request)
            $request = array_merge($_GET, $_POST);

        $elements = [];
        foreach ($fields as $key => $value) {
            $type = !is_array($value) ? $value : $value['type'];

            $attributes = ['value' => isset($request[$key]) ? $request[$key] : '', 'name' => $key];

            if( is_array($value) )
                $attributes = array_merge($value, $attributes);

            if(! isset($attributes['label']))
                $attributes['label'] = ucfirst($key);

            $elements[$key] = FormBuilder\get_element(
                $type, $attributes + ['label' => $attributes['label']]
            );
        }

        $form = new Form([
            'elements' => $elements
        ]);

        return $form;
    }


    /**
     * Get sortable js .
     *
     * @param array $attributes
     * @return string
     */
    public function getSortableJs($attributes = array()) {
        $url = isset($attributes['url']) ? $attributes['url'] : '';

        return <<<DOC
<script type='text/javascript'>
$(function() {
        function fixWidthHelper(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        }

        $("table tbody").sortable({
            helper: fixWidthHelper,
            update: function(event, ui) {
                var item = ui.item;

                var position = (item.prev('tr').length) ? 'after' : 'before';

                $.post('{$url}', {sortable: {
                    id: item.attr('data-id'), position: position, element: (item.prev('tr').length) ? item.prev('tr').attr('data-id') : item.next('tr').attr('data-id')
                }});         }
        }).disableSelection();
})
</script>
DOC;

    }

    /**
     * Include js scripts .
     *
     * @return string
     */
    protected function getJs() {
        return <<<JS
<script type='text/javascript'>
    var KEYCODE_ENTER = 13;
    var KEYCODE_ESC = 27;

    $(document).keyup(function(e) {
      if (e.keyCode == KEYCODE_ENTER) closeAll(true);
      if (e.keyCode == KEYCODE_ESC) closeAll(false);
    });

    $(".editable").on("click", function() {
        if( $(this).find(':input').length )
            return false;

        closeAll(true);

        var template = '<input type="text" class="form-control col-xs-3" id="" value='+ $(this).data('element') +' placeholder="Enter value">';

        $(this).html(template);
    });

    function closeAll(save) {
        $(".editable").has(':input').each(function(i, val) {
            var value = $(this).find(':input').val();
            $(this).html(value);

            if(save) {
                var url = '';
                if( $(val).data('url') )
                    url = $(val).data('url');
                else
                    url = '{$this->getUrl()}';

                var field = $(val).data('field');

                var dataObject = {};
                dataObject[field] = value;
                $.post(url, dataObject);
            }
        });
    }
</script>
JS;

    }


    /**
     * Render __toString magic f.
     *
     * @return string
     */
    public function __toString() {
        return $this->render();
    }

}