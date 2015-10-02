<?php

namespace Parfumix\TableManager\Drivers;

use Flysap\TableManager\DriverAble;
use Illuminate\Pagination\LengthAwarePaginator;

class Collection extends Driver implements DriverAble  {

    /**
     * Filter data .
     *
     * @param callable $filter
     * @param array $params
     * @return $this
     */
    public function filter(\Closure $filter, $params = array()) {
        $this->source['rows'] = $filter($this->source['rows'], $params);

        return $this;
    }

    /**
     * Get data .
     *
     * @param null $perPage
     * @return array
     */
    public function getData($perPage = null) {
        $source = $this->getSource();
        $items  = $source['rows'];

        $offSet              = (@$_GET['page'] * $perPage) - $perPage;
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);

        $paginator = (new LengthAwarePaginator(
            $itemsForCurrentPage, count($source['rows']), $perPage
        ));

        return [
            'columns' => $this->source['columns'],
            'rows'    => $paginator->getCollection(),
            'total'   => $paginator->total(),
        ];
    }

    /**
     * Return an array filter fields .
     *
     * @return mixed
     */
    public function filterFields() {
        return $this->source['columns'];
    }
}