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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Domain\Group\Query\GetDefaultGroups;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroup;
use PrestaShop\PrestaShop\Core\Search\Filters\GroupFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Configure\ShopParameters\Group\DefaultGroupsOptionsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages pages under "Configure > Shop parameters > Customer Settings > Groups" menu
 */
class GroupController extends FrameworkBundleAdminController
{
    /**
     * @param Request $request
     * @param GroupFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, GroupFilters $filters)
    {
        /** @var DefaultGroup $defaultGroups */
        $defaultGroups = $this->getQueryBus()->handle(new GetDefaultGroups());

        $grid = $this->get('prestashop.core.grid.factory.group')->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Groups/index.html.twig', [
            'grid' => $this->presentGrid($grid),
            'defaultGroups' => $defaultGroups,
        ]);
    }
}
