<?php

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
    protected $config_form = false;

    /**
     * List of hooks that must be registered by the module.
     * 
     * @var string[]
     */
    protected static $moduleHooks = [
        'displayFooter',
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
}
