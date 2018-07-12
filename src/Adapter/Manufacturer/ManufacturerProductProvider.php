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

use PrestaShop\PrestaShop\Adapter\Entity\Product;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Manufacturer\ManufacturerInterface;
use PrestaShop\PrestaShop\Core\Manufacturer\Provider\ManufacturerProductProviderInterface;

/**
 * @internal
 */
final class ManufacturerProductProvider implements ManufacturerProductProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProducts(ManufacturerInterface $manufacturer, LanguageInterface $language)
    {
        $products = $manufacturer->getProducts($language);
        $productsArray = [];

        foreach ($products as $product) {
            $productObj = new Product($product['id_product'], false, $language->getId());
            $productObj->loadStockData();

            $combinations = $productObj->getAttributeCombinations($language->getId());
            $combinationsData = [];

            foreach ($combinations as $combination) {
                $combinationsData[$combination['id_product_attribute']]['reference'] = $combination['reference'];
                $combinationsData[$combination['id_product_attribute']]['ean13'] = $combination['ean13'];
                $combinationsData[$combination['id_product_attribute']]['upc'] = $combination['upc'];
                $combinationsData[$combination['id_product_attribute']]['quantity'] = $combination['quantity'];
                $combinationsData[$combination['id_product_attribute']]['attributes'][] = [
                    $combination['group_name'],
                    $combination['attribute_name'],
                    $combination['id_attribute']
                ];
            }

            if (!empty($combinationsData)) {
                foreach ($combinationsData as $combinationId => $combination) {
                    $attributes = '';

                    foreach ($combination['attributes'] as list($groupName, $attributeName)) {
                        $attributes .= sprintf('%s - %s,', $groupName, $attributeName);
                    }

                    $combinationsData[$combinationId]['attributes'] = rtrim($attributes, ', ');
                }
            }

            $productsArray[] = [
                'id' => $product['id_product'],
                'name' => $product['name'],
                'reference' => $productObj->reference,
                'ean13' => $productObj->ean13,
                'upc' => $productObj->upc,
                'quantity' => $productObj->quantity,
                'combinations' => $combinationsData,
            ];
        }

        return $productsArray;
    }
}
