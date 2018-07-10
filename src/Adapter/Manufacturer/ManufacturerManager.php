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

use PrestaShop\PrestaShop\Adapter\Entity\Manufacturer;

/**
 * @internal
 */
class ManufacturerManager
{
    /**
     * Save manufacturer from given data
     *
     * @param array $data
     *
     * @return array
     */
    public function saveFromData(array $data)
    {
        $manufacturer = new Manufacturer(isset($data['id']) ? $data['id'] : null);
        $manufacturer->name = $data['name'];
        $manufacturer->description = $data['description'];
        $manufacturer->short_description = $data['short_description'];
        $manufacturer->meta_title = $data['meta_title'];
        $manufacturer->meta_description = $data['meta_description'];
        $manufacturer->meta_keywords = $data['meta_keywords'];
        $manufacturer->active = $data['active'];

        if (isset($data['shop_ids'])) {
            $manufacturer->id_shop_list = $data['shop_ids'];
        }

        // @todo: validate lang fields
        $errors = $manufacturer->validateFields(false, true);

        if (true !== $errors) {
            return [$errors];
        }

        $manufacturer->save();

        return [];
    }
}
