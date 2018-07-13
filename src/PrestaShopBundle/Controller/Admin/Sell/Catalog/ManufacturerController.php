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

use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopObjectNotFoundException;
use PrestaShop\PrestaShop\Adapter\Language\Language;
use PrestaShop\PrestaShop\Adapter\Manufacturer\Address;
use PrestaShop\PrestaShop\Adapter\Manufacturer\Manufacturer;
use PrestaShop\PrestaShop\Core\Filesystem\FileSizeCalculator;
use PrestaShop\PrestaShop\Core\Filesystem\FileSizeCalculatorInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Catalog\Manufacturer\ManufacturerAddressType;
use PrestaShopBundle\Form\Admin\Sell\Catalog\Manufacturer\ManufacturerType;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/listing.html.twig', [
            'layoutHeaderToolbarBtn' => [
                'add_manufacturer' => [
                    'href' => $this->generateUrl('admin_manufacturers_add'),
                    'desc' => $this->trans('Add new brand', 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                ],
                'add_manufacturer_address' => [
                    'href' => $this->generateUrl('admin_manufacturer_address_add'),
                    'desc' => $this->trans('Add new brand address', 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Add new manufacturer
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
            $errors = $manufacturerManager->save($manufacturerForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers');
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/form.html.twig', [
            'layoutTitle' => $this->trans('Add new', 'Admin.Actions'),
            'manufacturerForm' => $manufacturerForm->createView(),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Edit existing manufacturer
     *
     * @param Request $request
     * @param int $manufacturerId
     *
     * @return Response
     */
    public function editAction(Request $request, $manufacturerId)
    {
        try {
            $manufacturer = new Manufacturer($manufacturerId);
        } catch (PrestaShopObjectNotFoundException $e) {
            return $this->createNotFoundRedirectResponse('admin_manufacturers');
        }

        try {
            $thumbnailGenerator = $this->get('prestashop.adapter.manufacturer.thumbnail_generator');
            $thumbnail = $thumbnailGenerator->getThumbnail($manufacturer->getId());

            $fileSizeCalculator = $this->get('prestashop.core.filesystem.file_size_calculator');

            $thumbnailPath = $thumbnail->getPathname();
            $thumbnailSize = $fileSizeCalculator->getFormattedSize($thumbnail);
        } catch (FileNotFoundException $e) {
            $thumbnailPath = '';
            $thumbnailSize = '';
        }

        $manufacturerForm = $this->createForm(ManufacturerType::class, $manufacturer->toArray());
        $manufacturerForm->handleRequest($request);

        if ($manufacturerForm->isSubmitted()) {
            $manufacturerManager = $this->get('prestashop.adapter.manufacturer.manager');
            $errors = $manufacturerManager->save($manufacturerForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers');
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/form.html.twig', [
            'layoutTitle' => $this->trans('Edit: %value%', 'Admin.Catalog.Feature', ['%value%' => $manufacturer->getName()]),
            'manufacturerForm' => $manufacturerForm->createView(),
            'thumbnailPath' => $thumbnailPath,
            'thumbnailSize' => $thumbnailSize,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * View manufacturer products & addresses
     *
     * @param Request $request
     * @param int     $manufacturerId
     *
     * @return Response
     */
    public function viewAction(Request $request, $manufacturerId)
    {
        try {
            $language = new Language($this->getContext()->language->id);
            $manufacturer = new Manufacturer($manufacturerId);
        } catch (PrestaShopObjectNotFoundException $e) {
            return $this->createNotFoundRedirectResponse('admin_manufacturers');
        }

        $configuration = $this->get('prestashop.adapter.legacy.configuration');
        $manufacturerProductProvider = $this->get('prestashop.adapter.manufacturer.product_provider');

        $manufacturerProducts = $manufacturerProductProvider->getProducts($manufacturer, $language);
        $manufacturerAddresses = $manufacturer->getAddresses($language);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/view.html.twig', [
            'manufacturerProducts' => $manufacturerProducts,
            'manufacturerAddresses' => $manufacturerAddresses,
            'isStockManagementEnabled' => $configuration->get('PS_STOCK_MANAGEMENT'),
            'layoutTitle' => $manufacturer->getName(),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Add new manufacturer address
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addAddressAction(Request $request)
    {
        $addressForm = $this->createForm(ManufacturerAddressType::class);
        $addressForm->handleRequest($request);

        if ($addressForm->isSubmitted()) {
            $manufacturerManager = $this->get('prestashop.adapter.manufacturer.manager');
            $errors = $manufacturerManager->saveAddress($addressForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers');
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/address_form.html.twig', [
            'manufacturerAddressForm' => $addressForm->createView(),
        ]);
    }

    /**
     * Edit exiting manufacturer address
     *
     * @param Request $request
     * @param int     $addressId
     *
     * @return Response
     */
    public function editAddressAction(Request $request, $addressId)
    {
        try {
            $manufacturerAddress = new Address($addressId);
        } catch (PrestaShopObjectNotFoundException $e) {
            return $this->createNotFoundRedirectResponse('admin_manufacturers');
        }

        $addressForm = $this->createForm(ManufacturerAddressType::class, $manufacturerAddress->toArray());
        $addressForm->handleRequest($request);

        if ($addressForm->isSubmitted()) {
            $manufacturerManager = $this->get('prestashop.adapter.manufacturer.manager');
            $errors = $manufacturerManager->saveAddress($addressForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_manufacturers');
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/address_form.html.twig', [
            'manufacturerAddressForm' => $addressForm->createView(),
            'manufacturerAddress' => $manufacturerAddress,
        ]);
    }

    /**
     * Get response for not found entity
     *
     * @param string $notFoundRoute
     *
     * @return RedirectResponse
     */
    protected function createNotFoundRedirectResponse($notFoundRoute)
    {
        $this->addFlash('error', $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'));

        return $this->redirectToRoute($notFoundRoute);
    }
}
