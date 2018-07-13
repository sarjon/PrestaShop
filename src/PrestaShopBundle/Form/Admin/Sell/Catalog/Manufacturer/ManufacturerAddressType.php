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

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\RequiredFormFieldProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ManufacturerAddressType extends AbstractType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $manufacturerChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $countryChoiceProvider;

    /**
     * @var RequiredFormFieldProviderInterface
     */
    private $manufacturerAddressFieldProvider;

    /**
     * @param FormChoiceProviderInterface $manufacturerChoiceProvider
     * @param FormChoiceProviderInterface $countryChoiceProvider
     * @param RequiredFormFieldProviderInterface $manufacturerAddressFieldProvider
     */
    public function __construct(
        FormChoiceProviderInterface $manufacturerChoiceProvider,
        FormChoiceProviderInterface $countryChoiceProvider,
        RequiredFormFieldProviderInterface $manufacturerAddressFieldProvider
    ) {
        $this->manufacturerChoiceProvider = $manufacturerChoiceProvider;
        $this->countryChoiceProvider = $countryChoiceProvider;
        $this->manufacturerAddressFieldProvider = $manufacturerAddressFieldProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $formBuilder
            ->add('manufacturer', ChoiceType::class, [
                'choices' => $this->manufacturerChoiceProvider->getChoices(),
            ])
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('address1', TextType::class)
            ->add('address2', TextType::class, [
                'required' => false,
            ])
            ->add('postcode', TextType::class, [
                'required' => false,
            ])
            ->add('city', TextType::class)
            ->add('country', ChoiceType::class, [
                'choices' => $this->countryChoiceProvider->getChoices(),
            ])
            ->add('state', TextType::class, [
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'required' => false,
            ])
            ->add('phone_mobile', TextType::class, [
                'required' => false,
            ])
            ->add('other', TextareaType::class, [
                'required' => false,
            ])
        ;

        $requiredFields = $this->manufacturerAddressFieldProvider->getFields();
        if (in_array('company', $requiredFields)) {
            $formBuilder->add('company', TextType::class);
        }
    }
}
