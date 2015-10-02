<?php

namespace Parfumix\TableManager;

interface DriverAble {

    public function setSource($driver);

    public function getSource();

    public function getData($perPage = null);

    public function filter(\Closure $filter);
}