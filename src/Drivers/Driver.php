<?php

namespace Parfumix\TableManager\Drivers;

abstract class Driver {

    /**
     * @var
     */
    protected $source;

    /**
     * Set data source .
     *
     * @param $source
     * @return $this
     */
    public function setSource($source) {
        $this->source = $source;

        return $this;
    }

    /**
     * Get data source .
     *
     * @return mixed
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * Filter data source .
     *
     * @param callable $filter
     * @return $this
     */
    public function filter(\Closure $filter) {
        $this->source = $filter($this->getSource());

        return $this;
    }

    /**
     * Return an array filter fields .
     *
     * @return mixed
     */
    abstract public function filterFields();
}