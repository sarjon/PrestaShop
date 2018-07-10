<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Catalog\Brand\ManufacturerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ManufacturerController is responsible for Brands page
 */
class ManufacturerController extends FrameworkBundleAdminController
{
    /**
     * Show all brands & brand addresses
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/manufacturer_listing.html.twig', [
            'layoutHeaderToolbarBtn' => [
                'add_brand' => [
                    'href' => $this->generateUrl('admin_manufacturers_add'),
                    'desc' => $this->trans('Add new brand', 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                ],
                'add_brand_address' => [
                    'href' => $this->generateUrl('admin_manufacturers'),
                    'desc' => $this->trans('Add new brand address', 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Add new brand
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        $manufacturerForm = $this->createForm(ManufacturerType::class);
        $manufacturerForm->handleRequest($request);

        if ($manufacturerForm->isSubmitted()) {
            $manufacturerManager = $this->get('prestashop.adapter.manufacturer.manager');
            $errors = $manufacturerManager->saveFromData($manufacturerForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers');
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/manufacturer_form.html.twig', [
            'layoutTitle' => $this->trans('Add new', 'Admin.Actions'),
            'manufacturerForm' => $manufacturerForm->createView(),
        ]);
    }
}
