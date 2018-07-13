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

use PrestaShop\PrestaShop\Adapter\Entity\Manufacturer as ManufacturerLegacy;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopObjectNotFoundException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Manufacturer\ManufacturerInterface;

/**
 * @internal
 */
final class Manufacturer implements ManufacturerInterface
{
    /**
     * @var ManufacturerLegacy
     */
    private $manufacturer;

    /**
     * @param int      $manufacturerId
     */
    public function __construct($manufacturerId)
    {
        $this->manufacturer = new ManufacturerLegacy($manufacturerId);

        if (!$this->manufacturer->id) {
            throw new PrestaShopObjectNotFoundException(
                sprintf('Manufacturer with ID "%s" was not found.', $manufacturerId)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return (int) $this->manufacturer->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->manufacturer->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts(LanguageInterface $language)
    {
        return $this->manufacturer->getProductsLite($language->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getAddresses(LanguageInterface $language)
    {
        return $this->manufacturer->getAddresses($language->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        if (!$this->manufacturer->id) {
            return [];
        }

        return [
            'id' => $this->manufacturer->id,
            'name' => $this->manufacturer->name,
            'description' => $this->manufacturer->description,
            'short_description' => $this->manufacturer->short_description,
            'meta_title' => $this->manufacturer->meta_title,
            'meta_description' => $this->manufacturer->meta_description,
            'meta_keywords' => $this->manufacturer->meta_keywords,
            'active' => $this->manufacturer->active,
        ];
    }
}
