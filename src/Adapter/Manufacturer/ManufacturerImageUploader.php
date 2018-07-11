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

use PrestaShop\PrestaShop\Adapter\Entity\Context;
use PrestaShop\PrestaShop\Adapter\Entity\ImageManager;
use PrestaShop\PrestaShop\Adapter\Entity\ImageType;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @internal
 */
class ManufacturerImageUploader
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        ConfigurationInterface $configuration
    ) {
        $this->configuration = $configuration;
    }

    /**
     * @todo: refactor
     */
    public function upload($name, UploadedFile $file)
    {
        $errors = [];

        $manufacturerImgDir = $this->configuration->get('_PS_MANU_IMG_DIR_');
        $temporaryImgDir = $this->configuration->get('_PS_TMP_IMG_DIR_');

        ImageManager::resize(
            $file->getPathname(),
            $manufacturerImgDir.$name.'.jpg'
        );

//        if ($error = ImageManager::validateUpload($_FILES[$name], Tools::getMaxUploadSize())) {
//            $errors[] = $error;
//
//            return $errors;
//        }

        $tmpName = tempnam($temporaryImgDir, 'PS');
        if (!$tmpName) {
            $errors[] = [
                'key' => 'An error occurred while uploading the image.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ];

            return $errors;
        }

        if (!move_uploaded_file($file->getPathname(), $tmpName)) {
            $errors[] = [
                'key' => 'An error occurred while uploading the image.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ];

            return $errors;
        }

        if (!ImageManager::checkImageMemoryLimit($tmpName)) {
            $errors[] = [
                'key' => 'Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ];

            return $errors;
        }

        if (!ImageManager::resize($tmpName, $manufacturerImgDir.$name.'.jpg')) {
            $errors[] = [
                'key' => 'An error occurred while uploading the image.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ];

            return $errors;
        }

        return $this->generateHighResolutionImages($name);
    }

    private function generateHighResolutionImages($name)
    {
        $result = true;

        $manufacturerImgDir = $this->configuration->get('_PS_MANU_IMG_DIR_');
        $generateHighDpiImages = (bool) $this->configuration->get('PS_HIGHT_DPI');
        $temporaryImgDir = $this->configuration->get('_PS_TMP_IMG_DIR_');

        $imagesTypes = ImageType::getImagesTypes('manufacturers');

        foreach ($imagesTypes as $imagesType) {
            $result &= ImageManager::resize(
                $manufacturerImgDir.$name.'.jpg',
                $manufacturerImgDir.$name.'-'.stripslashes($imagesType['name']).'.jpg',
                (int) $imagesType['width'],
                (int) $imagesType['height']
            );

            if ($generateHighDpiImages) {
                $result &= ImageManager::resize(
                    $manufacturerImgDir.$name.'.jpg',
                    $manufacturerImgDir.$name.'-'.stripslashes($imagesType['name']).'2x.jpg',
                    (int) $imagesType['width'] * 2,
                    (int) $imagesType['height'] * 2
                );
            }
        }

        $currentLogoFile = $temporaryImgDir.'manufacturer_mini_'.$name.'_'.Context::getContext()->shop->id.'.jpg';

        if ($result && file_exists($currentLogoFile)) {
            unlink($currentLogoFile);
        }

        $errors = [];

        if (!$result) {
            $errors[] = [
                'key' => 'Unable to resize one or more of your pictures.',
                'parameters' => [],
                'domain' => 'Admin.Catalog.Notification',
            ];
        }

        return $errors;
    }
}
