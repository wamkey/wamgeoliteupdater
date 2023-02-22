<?php

namespace Wamkey\GeoLiteUpdater\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use Symfony\Component\HttpFoundation\Response;
use Wamkey\GeoLiteUpdater\GeoLite\GeoLite;
use Wamkey\GeoLiteUpdater\GeoLite\GithubFetcher;
use Wamkey\GeoLiteUpdater\GeoLite\Installer;
use Wamkey\GeoLiteUpdater\GeoLite\Updater;

/**
 * @ModuleActivated(moduleName="wamgeoliteupdater", redirectRoute="admin_module_manage")
 */
class GeoLiteController extends FrameworkBundleAdminController
{
    /**
     * @var GeoLite
     */
    protected $geoLite;

    public function __construct()
    {
        parent::__construct();

        $this->geoLite = GeoLite::fromPath(_PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_modules_manage"
     * )
     * @return Response
     */
    public function index(): Response
    {
        $updater = new Updater($this->geoLite, new GithubFetcher());

        return $this->render('@Modules/wamgeoliteupdater/views/templates/admin/geolite-index.html.twig', [
            'geoIpMeta' => $this->geoLite->getFormattedMetadata(),
            'updateAvailable' => $updater->isOutdated(),
            'updateUrl' => $this->generateUrl('wamgeoliteupdater_geolite_update'),
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="wamgeoliteupdater_geolite"
     * )
     * @return Response
     */
    public function update(): Response
    {
        $updater = new Updater($this->geoLite, new GithubFetcher());
        $downloadPath = $updater->update();

        $installer = new Installer($this->geoLite, $downloadPath);
        $installer->install();

        $this->addFlash('success', $this->trans('File downloaded successfully', 'Modules.Wamgeoliteupdater.Admin'));
        return $this->redirect($this->generateUrl('wamgeoliteupdater_geolite'));
    }
}
