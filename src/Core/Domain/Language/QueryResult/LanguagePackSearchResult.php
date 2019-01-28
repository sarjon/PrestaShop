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

namespace PrestaShop\PrestaShop\Core\Domain\Language\QueryResult;

class LanguagePackSearchResult
{
    /**
     * @var string
     */
    private $languageName;

    /**
     * @var string
     */
    private $languagePackDownloadUrl;

    /**
     * @var string
     */
    private $languagePackVersion;

    /**
     * @param string $languageName
     * @param string $languagePackVersion
     * @param string $languagePackDownloadUrl
     */
    public function __construct($languageName, $languagePackVersion, $languagePackDownloadUrl)
    {
        $this->languageName = $languageName;
        $this->languagePackDownloadUrl = $languagePackDownloadUrl;
        $this->languagePackVersion = $languagePackVersion;
    }

    /**
     * @return string
     */
    public function getLanguageName()
    {
        return $this->languageName;
    }

    /**
     * @return string
     */
    public function getLanguagePackDownloadUrl()
    {
        return $this->languagePackDownloadUrl;
    }

    /**
     * @return string
     */
    public function getLanguagePackVersion()
    {
        return $this->languagePackVersion;
    }
}
