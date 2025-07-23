<?php

declare(strict_types=1);

namespace Wamkey\GeoLiteUpdater\Component;

/**
 * Implementation of a view component based on Smarty.
 */
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
    abstract public function render(): string;
}
