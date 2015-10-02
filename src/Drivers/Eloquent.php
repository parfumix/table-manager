<?php

namespace Parfumix\TableManager\Drivers;

use Parfumix\TableManager\DriverAble;
use Illuminate\Support\Collection;

class Eloquent extends Driver implements DriverAble {

    /**
     * Get source data .
     *
     * @param null $perPage
     * @return array
     */
    public function getData($perPage = null) {
        $source = $this->getSource();

        $paginator = $source->paginate($perPage);
        $rows      = $paginator->getCollection();

        $fields = [];

        $columns = $this->getSource()->getModel()->getFillable();
        if( array_key_exists('skyShow', get_class_methods(get_class($this->getSource()->getModel()))))
            $columns = $this->getSource()->getModel()->skyShow();

        foreach ($rows as $key => $row) {
            foreach ($columns as $columnKey => $column) {
                if( is_numeric($columnKey) )
                    $columnKey = $column;

                $value = $row->$columnKey;

                if( $value instanceof Collection ) {
                    $attribute = [];
                    foreach ($value as $row)
                        $attribute[] = ($row->{str_singular($columnKey)});
                } else {
                    $attribute = $value;
                }

                $fields[$key][$columnKey] = $attribute;
            }
        }

        return [
            'columns' => $columns,
            'rows'    => $fields,
            'total'   => $paginator->total(),
        ];
    }

    /**
     * Return an array filter fields .
     *
     * @return mixed
     */
    public function filterFields() {
        $columns = $this->getSource()->getModel()->getFillable();
        if( array_key_exists('skyFilter', get_class_methods(get_class($this->getSource()->getModel()))))
            $columns = $this->getSource()->getModel()->skyFilter();

        return $columns;
    }
}