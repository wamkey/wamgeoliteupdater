<?php

namespace Wamkey\GeoLiteUpdater\Manager;

use Wamkey\GeoLiteUpdater\Component\Component;
use Wamkey\GeoLiteUpdater\GeoLite\GeoLite;

class ConfigurationManager extends Component
{
    protected $template = 'module:wamgeoliteupdater/views/templates/components/configuration-manager.tpl';

    /**
     * @var GeoLite
     */
    protected $geoLite;

    /**
     * @var \Link
     */
    protected $link;

    public function __construct(GeoLite $geoLite, \Link $link)
    {
        parent::__construct();

        $this->geoLite = $geoLite;
        $this->link = $link;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->smarty->fetch($this->template, [
            'geoIpMeta' => $this->geoLite->getFormattedMetadata(),
            'configUrl' => $this->link->getAdminLink('AdminWamGeoLite', true, [
                'route' => 'wamgeoliteupdater_geolite',
            ]),
        ]);
    }
}
