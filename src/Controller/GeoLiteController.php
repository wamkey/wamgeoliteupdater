<?php

namespace Wamkey\GeoLiteUpdater\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wamkey\GeoLiteUpdater\Database\GeoLite;

/**
 * @ModuleActivated(moduleName="wamgeoliteupdater", redirectRoute="admin_module_manage")
 */
class GeoLiteController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_modules_manage"
     * )
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $geoLite = GeoLite::fromPath(_PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_);

        return $this->render('@Modules/wamgeoliteupdater/views/templates/admin/geolite-index.html.twig', [
            'geoIpMeta' => $geoLite->getFormattedMetadata(),
        ]);
    }
}
