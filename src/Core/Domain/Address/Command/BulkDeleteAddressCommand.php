<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\Command;

use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Deletes addresses in bulk action
 */
class BulkDeleteAddressCommand
{
    /**
     * @var AddressId[]
     */
    private $addressIds;

    /**
     * @param int[] $addressIds
     *
     * @throws AddressConstraintException
     */
    public function __construct($addressIds)
    {
        $this->setAddressIds($addressIds);
    }

    /**
     * @return AddressId[]
     */
    public function getAdressIds()
    {
        return $this->addressIds;
    }

    /**
     * @param int[] $addressIds
     *
     * @throws AddressConstraintException
     */
    private function setAddressIds(array $addressIds)
    {
        foreach ($addressIds as $addressId) {
            $this->addressIds[] = new AddressId((int) $addressId);
        }
    }
}