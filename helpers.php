<?php

namespace Parfumix\TableManager;

use Parfumix\FormBuilder;

/**
 * Create table .
 *
 * @param $driver
 * @param $source
 * @param array $tableAttr
 * @return mixed
 */
function table($source, $driver = null, array $tableAttr = array()) {
    $driverManager = (new DriverManager(
        config('table-manager')
    ));

    $driver = $driverManager->driver(strtolower($driver));
    $driver->setSource($source);

    return (new Table($driver))
        ->setAttributes($tableAttr);
}

/**
 * Draw pagination
 *
 * @param Table $table
 * @param null $perPage
 * @param array $appends
 * @return mixed
 */
function render_pagination(Table $table, $perPage = null, array $appends = array()) {
    return $table->paginate($perPage, $appends);
}

/**
 * Get form filter .
 *
 * @param Table $table
 * @return \Parfumix\FormBuilder\Form
 */
function get_filter_form(Table $table) {
    return $table->renderFilter();
}

/**
 * Render filter form .
 *
 * @param Table $table
 * @param array $attributes
 * @return string
 */
function render_filter_form(Table $table, $attributes = array()) {
    $form = get_filter_form($table);

    foreach ($attributes as $key => $value)
        if( method_exists($form, $key) )
            $form->{$key}($value);

    if( isset($attributes['group']) )
        $groups = $form->getGroup($attributes['group']);
    else
        $groups = $form->getGroups();

    $html = $form->openForm();

    array_walk($groups, function($value, $group) use(& $html, $form) {
        $html .= '<div class="'.$group.'">';
        $html .= $form->render($group, false);
        $html .= '</div>';
    });

    $html .= FormBuilder\get_element('button', ['value' => _("Search"), 'type' => 'submit'])
        ->render();

    $html .= $form->closeForm();

    return $html;
}