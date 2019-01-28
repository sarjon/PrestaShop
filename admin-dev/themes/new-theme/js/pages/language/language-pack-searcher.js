/**
 * 2007-2019 PrestaShop
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

export default class LanguagePackSearcher {
  constructor() {
    this.$isoCodeInput = $('#language_iso_code');

    this._init();

    return {};
  }

  _init() {
    this.$isoCodeInput.on('input', (event) => {
      const $input = $(event.currentTarget);
      const isoCode = $input.val();

      this._doSearch(isoCode);
    });
  }

  _doSearch(languageIsoCode) {
    if (languageIsoCode.length !== 2) {
      return;
    }

    this._hideLanguagePack();
    this._showLoader();

    $.ajax(this.$isoCodeInput.data('search-language-pack-url'), {
      data: {
        iso_code: languageIsoCode
      }
    }).then((response) => {
      this._hideLoader();

      this._showLanguagePack(
        response.language_name,
        response.language_pack_version,
        response.language_pack_download_url,
      );
    });
  }

  _showLanguagePack(languageName, languagePackVersion, languagePackDownloadUrl) {
    const $languagePackBlock = $('.js-language-search-alert');

    $languagePackBlock.find('.js-language-name').text(languageName);
    $languagePackBlock.find('.js-language-pack-version').text(languagePackVersion);
    $languagePackBlock.find('.js-language-pack-download-url').attr('href', languagePackDownloadUrl);

    $languagePackBlock.removeClass('d-none');
  }

  _hideLanguagePack() {
    $('.js-language-search-alert').addClass('d-none');
  }

  _showLoader() {
    $('.js-language-pack-loader').removeClass('d-none');
  }

  _hideLoader() {
    $('.js-language-pack-loader').addClass('d-none');
  }
}
