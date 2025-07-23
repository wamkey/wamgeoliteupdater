<?php

declare(strict_types=1);

use Wamkey\GeoLiteUpdater\GeoLite\GeoLite;
use Wamkey\GeoLiteUpdater\Manager\ConfigurationManager;

if(! defined('_PS_VERSION_')) {
    exit;
}

class WamGeoLiteUpdater extends Module
{
    /**
     * @inheritDoc
     */
    protected $tabs = [
        [
            'route_name' => 'wamgeoliteupdater_geolite',
            'class_name' => 'AdminWamGeoLite',
            'visible' => true,
            'parent_class_name' => 'AdminParentLocalization',
            'name' => [
                'en' => 'GeoLite configuration',
                'fr' => 'Configuration GeoLite',
            ],
        ],
        [
            'route_name' => 'wamgeoliteupdater_geolite_update',
            'class_name' => 'AdminWamGeoLiteUpdate',
            'visible' => false,
            'parent_class_name' => 'AdminParentLocalization',
            'name' => [
                'en' => 'Update GeoLite database',
                'fr' => 'Mettre à jour la base de données GeoLite',
            ],
        ],
    ];

    public function __construct()
    {
        $this->name = 'wamgeoliteupdater';
        $this->tab = 'administration';
        $this->version = '1.0.4';
        $this->author = 'Tech WAM';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('GeoLite updater', [], 'Modules.Wamgeoliteupdater.Main');
        $this->description = $this->trans('GeoLite2 database updater', [], 'Modules.Wamgeoliteupdater.Main');
        $this->ps_versions_compliancy = ['min' => '1.7.7', 'max' => _PS_VERSION_];
    }

    /**
     * @inheritDoc
     */
    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /**
     * Installs the module.
     *
     * @return bool
     */
    public function install(): bool
    {
        // Set context in order to install the module in all shops, even if the selected shop is different.
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install();
    }

    /**
     * Uninstalls the module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        // Set context in order to install the module in all shops, even if the selected shop is different.
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::uninstall();
    }

    /**
     * Returns the current context instance.
     *
     * @return \Context
     */
    public function getContext(): \Context
    {
        return $this->context;
    }

    /**
     * Returns the HTML output of the module's configuration page.
     *
     * @return string
     */
    public function getContent(): string
    {
        return (new ConfigurationManager(
            GeoLite::fromPath(_PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_),
            $this->getContext()->link
        ))->render();
    }
}
