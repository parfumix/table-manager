<?php

namespace Parfumix\TableManager\Traits;

use Parfumix\TableManager\Column;

trait Columns {

    protected $columns = [];

    /**
     * Add new column .
     *
     * @param $attributes
     * @param $slug
     * @return $this
     * @internal param Column $column
     */
    public function addColumn($attributes, $slug) {
        if( is_numeric($slug) ) {
            $slug = $attributes; $attributes = [];
        }

        if(! isset($columns[$slug]))
            $this->columns[$slug] = new Column($slug, $attributes);

        return $this;
    }

    /**
     * Add new columns .
     *
     * @param array $columns
     * @return $this
     */
    public function addColumns($columns = array()) {
        if(! is_array($columns))
            $columns = (array)$columns;

        array_walk($columns, function($value, $key) {
            $this->addColumn($value, $key);
        });

        return $this;
    }

    /**
     * Get columns .
     *
     * @return mixed
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * Get column by slug .
     *
     * @param $title
     * @return mixed
     */
    public function getColumn($title) {
        if( isset($this->columns[$title]) )
            return $this->columns[$title];
    }

}