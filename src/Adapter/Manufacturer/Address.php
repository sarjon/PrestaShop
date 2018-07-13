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

namespace PrestaShop\PrestaShop\Adapter\Manufacturer;

use PrestaShop\PrestaShop\Adapter\Entity\ManufacturerAddress;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopObjectNotFoundException;
use PrestaShop\PrestaShop\Core\Manufacturer\AddressInterface;

/**
 * @internal
 */
final class Address implements AddressInterface
{
    /**
     * @var ManufacturerAddress
     */
    private $address;

    /**
     * @param int $addressId
     */
    public function __construct($addressId)
    {
        $this->address = new ManufacturerAddress($addressId);

        if (!$this->address->id || !$this->address->id_manufacturer) {
            throw new PrestaShopObjectNotFoundException(
                sprintf('Manufacturer address with ID "%s" was not found', $addressId)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return (int) $this->address->id;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'id' => $this->address->id,
            'manufacturer' => $this->address->id_manufacturer,
            'first_name' => $this->address->firstname,
            'last_name' => $this->address->lastname,
            'company' => $this->address->company,
            'address1' => $this->address->address1,
            'address2' => $this->address->address2,
            'postcode' => $this->address->postcode,
            'city' => $this->address->city,
            'country' => $this->address->id_country,
            'state' => $this->address->id_state,
            'phone_mobile' => $this->address->phone_mobile,
            'phone' => $this->address->phone,
            'other' => $this->address->other,
        ];
    }
}
