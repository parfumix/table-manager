<?php

namespace Parfumix\TableManager;

use Flysap\Support\Traits\ElementAttributes;
use Flysap\Support\Traits\ElementsTrait;

class Row {

    use ElementAttributes, ElementsTrait;

    /**
     * @var array
     */
    private $columns;

    /**
     * @param array $elements
     * @param array $columns
     */
    public function __construct($elements, $columns = array()) {
        $this->setElements(
            $elements
        );

        $this->setColumns($columns);
    }

    /**
     * Render column
     *
     */
    public function render() {
        $html = '<tr ';
        $html .= $this->renderAttributes();

        if( $this->getColumn('id') )
            if( $value = $this->getElement('id') )
                $html .= ' data-id="' . $value . '"';

        $html .= '>';

        $columns = $this->getColumns();

        array_walk($columns, function($column, $title) use(& $html) {
            if( ! $column->isAllowed() )
                return false;

            $html .= '<td';

            $value = $this->getElement($title);

            $value = $column->hasClosure() ? $column->runClosure($value, ['elements' => $this->getElements()]) : $value;

            if( is_array($value) )
                $value = implode('<br />', $value);

            if( $column->isEditable() )
                $html .= " data-element='{$value}' class='editable'";

            if( $column->hasAttribute('url'))
                $html .= " data-url='{$column->getAttribute('url')}' data-field='{$column->getTitle()}'";

            $html .= '>';

            if( $column->hasAttribute('template') ) {
                $template = $column->getAttribute('template');

                if($template instanceof \Closure)
                    $template = $template($value);
                else
                    $template = str_replace('%s', $value, $template);

                $html .= $template;
            } else {
                $html .= $column->hasAttribute('before') ? $column->getAttribute('before') : '';
                $html .= $value;
                $html .= $column->hasAttribute('after') ? $column->getAttribute('after') : '';
            }

            $html .= '</td>';
        });

        $html .= '</tr>';

        return $html;
    }


    /**
     * Set columns .
     *
     * @param $columns
     * @return $this
     */
    public function setColumns($columns) {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get columns .
     *
     * @return array
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * Get column by key .
     *
     * @param $key
     */
    public function getColumn($key) {
        if( isset($this->columns[$key]) )
            return $this->columns[$key];
    }


    /**
     * Render column .
     */
    public function __toString() {
        return $this->render();
    }
}