<?php

namespace Parfumix\TableManager;

use Illuminate\Support\ServiceProvider;
use Flysap\Support;

class TableServiceProvider extends ServiceProvider {

    /**
     * On boot's application load package requirements .
     */
    public function boot() {
        $this->publishes([
            __DIR__.'/../configuration' => config_path('yaml/table-manager'),
        ]);

        $this->loadConfiguration();

        Support\merge_yaml_config_from(
            config_path('yaml/table-manager/general.yaml') , 'table-manager'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() { }

    /**
     * Load configuration .
     *
     * @return $this
     */
    protected function loadConfiguration() {
        Support\set_config_from_yaml(
            __DIR__ . '/../configuration/general.yaml' , 'table-manager'
        );

        return $this;
    }
}

