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

namespace PrestaShopBundle\Form\Admin\Sell\Catalog\Manufacturer;

use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslateTextType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ManufacturerType extends TranslatorAwareType
{
    /**
     * @var FeatureInterface
     */
    private $multishopFeature;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FeatureInterface $multishopFeature
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FeatureInterface $multishopFeature
    ) {
        parent::__construct($translator, $locales);

        $this->multishopFeature = $multishopFeature;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('short_description', TranslateType::class, [
                'type' => FormattedTextareaType::class,
                'options' => [
                    'required' => false,
                ],
                'locales' => $this->locales,
                'hideTabs' => false,
                'required' => true,
            ])
            ->add('description', TranslateType::class, [
                'type' => FormattedTextareaType::class,
                'options' => [
                    'required' => false,
                ],
                'locales' => $this->locales,
                'hideTabs' => false,
                'required' => true,
            ])
            ->add('logo', FileType::class, [
                'required' => false,
            ])
            ->add('meta_title', TranslateTextType::class, [
                'locales' => $this->locales,
            ])
            ->add('meta_description', TranslateType::class, [
                'type' => FormattedTextareaType::class,
                'options' => [
                    'required' => false,
                ],
                'locales' => $this->locales,
                'hideTabs' => false,
                'required' => true,
            ])
            ->add('meta_keywords', TranslateTextType::class, [
                'locales' => $this->locales,
            ])
            ->add('active', SwitchType::class, [
                'data' => false,
            ])
        ;

        if ($this->multishopFeature->isActive()) {
            $builder->add('shop_ids', MaterialChoiceTreeType::class);
        }
    }
}
