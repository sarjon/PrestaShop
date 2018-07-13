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

namespace PrestaShop\PrestaShop\Core\Filesystem;

use Error;
use Symfony\Component\HttpFoundation\File\File;

final class FileSizeCalculator implements FileSizeCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSize(File $file, $sizeIn = self::SIZE_MB)
    {
        $sizes = [
            self::SIZE_B,
            self::SIZE_KB,
            self::SIZE_MB,
            self::SIZE_GB,
            self::SIZE_TB,
            self::SIZE_PB,
        ];

        if (!in_array($sizeIn, $sizes)) {
            throw new Error(
                sprintf('Unsupported "%s" file size. Supported file sizes are: %s', $sizeIn, implode(',', $sizes))
            );
        }

        $fileSize = $file->getSize();

        if (self::SIZE_B === $sizeIn) {
            return $fileSize;
        }

        foreach ($sizes as $size) {
            if (self::SIZE_B === $size) {
                continue;
            }

            $fileSize /= 1024;

            if ($size === $sizeIn) {
                break;
            }
        }

        return $fileSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedSize(File $file, $sizeIn = self::SIZE_MB)
    {
        $size = $this->getSize($file, $sizeIn);

        return sprintf('%s %s', number_format($size, 2), strtoupper($sizeIn));
    }
}
