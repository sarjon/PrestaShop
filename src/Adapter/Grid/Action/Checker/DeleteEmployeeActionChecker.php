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

namespace PrestaShop\PrestaShop\Adapter\Grid\Action\Checker;

use Context;
use Employee;
use PrestaShop\PrestaShop\Core\Grid\Action\ActionCheckerInterface;

/**
 * Class DeleteEmployeeActionChecker checks if "delete" action can be applied to given employee.
 */
final class DeleteEmployeeActionChecker implements ActionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canApplyOn(array $employee)
    {
        $cannotBeDeletedAdmins = [Context::getContext()->employee->id];
        $superAdminCount = (int) Employee::countProfile(_PS_ADMIN_PROFILE_, true);

        if (1 === $superAdminCount) {
            $superAdmins = Employee::getEmployeesByProfile(_PS_ADMIN_PROFILE_, true);

            foreach ($superAdmins as $val) {
                $cannotBeDeletedAdmins[] = $val['id_employee'];
            }
        }

        return !in_array((int) $employee['id_employee'], $cannotBeDeletedAdmins);
    }
}
