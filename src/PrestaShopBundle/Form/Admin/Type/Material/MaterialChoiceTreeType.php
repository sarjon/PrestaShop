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

namespace PrestaShopBundle\Form\Admin\Type\Material;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialChoiceTreeType extends ChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        $this->getChoices($options['choices_tree'], $choices, $options['ignore_parent']);

        $builder->add('choices', ChoiceType::class, [
            'choices' => $choices,
            'expanded' => true,
            'multiple' => $options['multiple'],
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['choices_tree'] = $options['choices_tree'];
        $view->vars['multiple'] = $options['multiple'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => true,
            'choices' => [],
            'choices_tree' => [],
            'ignore_parent' => true,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'material_choice_tree';
    }

    private function getChoices(array $choiceTree, array &$choices, $ignoreParent)
    {
        foreach ($choiceTree as $choice) {
            if (!$ignoreParent || ($ignoreParent && empty($choice['children']))) {
                $choices[$choice['name']] = $choice['value'];
            }

            if (!empty($choice['children'])) {
                $this->getChoices($choice['children'], $choices, $ignoreParent);
            }
        }
    }
}
