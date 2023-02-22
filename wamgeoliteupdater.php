<?php

use Wamkey\GeoLiteUpdater\GeoLite\GeoLite;
use Wamkey\GeoLiteUpdater\Manager\ConfigurationManager;

if (! defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_ . 'wamgeoliteupdater/vendor/autoload.php');

class WamGeoLiteUpdater extends Module
{
    /**
     * Defines if the module has a configuration page (PrestaShop-specific property).
     * 
     * @var bool
     */
    protected $config_form = true;

    /**
     * @inheritDoc
     */
    public $tabs = [
        [
            'route_name' => 'wamgeoliteupdater_geolite',
            'class_name' => 'AdminWamGeoLite',
            'visible' => false,
            'parent_class_name' => 'DEFAULT',
            'wording' => 'GeoLite configuration',
            'wording_domain' => 'Modules.Wamgeoliteupdater.Admin',
        ],
        [
            'route_name' => 'wamgeoliteupdater_geolite_update',
            'class_name' => 'AdminWamGeoLiteUpdate',
            'visible' => false,
            'parent_class_name' => 'AdminWamGeoLite',
            'wording' => 'Update GeoLite database',
            'wording_domain' => 'Modules.Wamgeoliteupdater.Admin',
        ],
    ];

    public function __construct()
    {
        $this->name = 'wamgeoliteupdater';
        $this->tab = 'administration';
        $this->version = '0.1.0';
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
