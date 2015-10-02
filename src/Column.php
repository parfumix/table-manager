<?php

namespace Parfumix\TableManager;

use Flysap\Support\Traits\ElementAttributes;
use Flysap\Support\Traits\ElementPermissions;
use Flysap\Support;

class Column {

    use ElementAttributes, ElementPermissions;

    private $title;

    /**
     * @var callable
     */
    protected $closure;

    /**
     * @var
     */
    protected $relation;

    /**
     * @param $title
     * @param array $attributes
     */
    public function __construct($title, $attributes = array()) {
        if(! is_array($attributes))
            $attributes = (array)$attributes;

        $this->title = $title;

        if( $attributes instanceof \Closure )
            $this->closure = $attributes;
        else
            $this->setAttributes($attributes);

        /** Set roles */
        if( $this->getAttribute('roles') )
            $this->roles(
                $this->getAttribute('roles')
            );

        /** Set permissions  */
        if( $this->getAttribute('permissions') )
            $this->permissions(
                $this->getAttribute('permissions')
            );
    }

    /**
     * Get title .
     *
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set title .
     *
     * @param $title
     * @return $this
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }


    /**
     * Set closure .
     *
     * @param callable $closure
     * @return $this
     */
    public function setClosure(\Closure $closure) {
        $this->closure = $closure;

        return $this;
    }

    /**
     * Check if has closure ..
     *
     * @return callable
     */
    public function hasClosure() {
        return (! is_null($this->closure)) || $this->hasAttribute('closure');
    }

    /**
     * @param null $value
     * @return callable
     */
    public function getClosure($value = null) {
        if(! $closure = $this->closure)
            $closure = $this->getAttribute('closure');

        if(! is_null($value))
            return $closure($value);

        return $closure;
    }

    /**
     * Run closure .
     *
     * @param null $value
     * @param array $params
     * @return mixed
     */
    public function runClosure($value = null, $params = []) {
        if(! $closure = $this->closure)
            $closure = $this->getAttribute('closure');

        return $closure($value, $params);
    }


    /**
     * Is editable ?
     *
     * @return bool
     */
    public function isEditable() {
        return $this->hasAttribute('editable') && $this->getAttribute('editable') === true;
    }

    /**
     * Is sortable ?
     *
     * @return bool
     */
    public function isSortable() {
        return $this->hasAttribute('sortable');
    }


    /**
     * Render column
     *
     */
    public function render() {
        if( ! $this->isAllowed() )
            return false;

        if( $this->hasAttribute('label') ) {
            $title = $this->getAttribute('label');
        }  else {
            $title = $this->getTitle();
        }

        $html = '<th ';
        $html .= $this->renderAttributes(['class', 'id']);
        $html .= '>';


        if( $this->isSortable() ) {
            $sortable = $this->getAttribute('sortable');
            $slug     = $this->getTitle();;

            if( ! $sortable instanceof \Closure )
                $sortable = function($title) use($slug) {
                    $append_url = ['sort' => $slug];

                    if( isset($_GET['direction']) && $_GET['direction'] == 'asc' )
                        $append_url['direction'] = 'desc';
                    else
                        $append_url['direction'] = 'asc';

                    return '<a href="?'.Support\append_query_url($append_url).'">'.$title.'</a>';
                };

            $html .= $sortable($title);
        } else {
            $html .= $title;
        }

        $html .= '</th>';

        return $html;
    }

    /**
     * Render column .
     */
    public function __toString() {
        return $this->render();
    }

}