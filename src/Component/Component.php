<?php

namespace Wamkey\GeoLiteUpdater\Component;

abstract class Component
{
    /**
     * @var \Smarty
     */
    protected $smarty;

    public function __construct()
    {
        /**
         * @var \WamGeoLiteUpdater $module
         */
        $module = \Module::getInstanceByName('wamgeoliteupdater');
        $this->smarty = $module->getContext()->smarty;
    }

    /**
     * Renders a component.
     *
     * @return string
     */
    public abstract function render(): string;
}
