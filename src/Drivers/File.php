<?php

namespace Parfumix\TableManager\Drivers;

use Flysap\Support;
use Parfumix\TableManager\DriverAble;
use Parfumix\TableManager\TableException;

class File extends Collection implements DriverAble  {

    public function __construct($source) {
        if (! Support\is_path_exists(
            app_path('../' . $source)
        ))
            throw new TableException(_('Invalid path config file'));

        $source = require app_path('../' . $source);

        $this->setSource($source);
    }

}