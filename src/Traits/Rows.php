<?php

namespace Parfumix\TableManager\Traits;

use Parfumix\TableManager\Row;
use Illuminate\Support\Collection;

trait Rows {

    protected $rows = [];

    /**
     * Add new Row .
     *
     * @param Row $row
     * @param $columns
     * @return $this
     */
    public function addRow($row, $columns) {
        $this->rows[] = new Row($row, $columns);;

        return $this;
    }

    /**
     * Add new rows .
     *
     * @param array $rows
     * @param $columns
     * @return $this
     */
    public function addRows($rows = array(), $columns) {
        if(! is_array($rows) && ! $rows instanceof Collection)
            $rows = (array)$rows;

        foreach($rows as $row)
            $this->addRow($row, $columns);

        return $this;
    }

    /**
     * Get rows .
     *
     * @return mixed
     */
    public function getRows() {
        return $this->rows;
    }
}