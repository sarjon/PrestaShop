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

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Creates customers group definition
 */
final class GroupGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'group';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Groups', [], 'Admin.Global');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('group_bulk'))
                ->setName($this->trans('Group name', [], 'Admin.Shopparameters.Feature'))
                ->setOptions([
                    'bulk_field' => 'id_group',
                ])
            )
            ->add((new DataColumn('id_group'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_group',
                ])
            )
            ->add((new DataColumn('name'))
                ->setName($this->trans('Group name', [], 'Admin.Shopparameters.Feature'))
                ->setOptions([
                    'field' => 'name',
                ])
            )
            ->add((new DataColumn('reduction'))
                ->setName($this->trans('Discount (%)', [], 'Admin.Shopparameters.Feature'))
                ->setOptions([
                    'field' => 'reduction',
                ])
            )
            ->add((new DataColumn('nb'))
                ->setName($this->trans('Members', [], 'Admin.Shopparameters.Feature'))
                ->setOptions([
                    'field' => 'nb',
                ])
            )
            ->add((new DataColumn('show_prices'))
                ->setName($this->trans('Show prices', [], 'Admin.Shopparameters.Feature'))
                ->setOptions([
                    'field' => 'show_prices',
                ])
            )
            ->add((new DataColumn('date_add'))
                ->setName($this->trans('Creation date', array(), 'Admin.Shopparameters.Feature'))
                ->setOptions([
                    'field' => 'date_add',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_languages_edit',
                                'route_param_name' => 'languageId',
                                'route_param_field' => 'id_lang',
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                                'route' => 'admin_languages_index',
                                'route_param_name' => 'languageId',
                                'route_param_field' => 'id_lang',
                            ])
                        ),
                ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_group', NumberType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->translator->trans('Search ID', [], 'Admin.Shopparameters.Help'),
                    ],
                ])
                ->setAssociatedColumn('id_group')
            )
            ->add((new Filter('name', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->translator->trans('Search name', [], 'Admin.Shopparameters.Help'),
                    ],
                ])
                ->setAssociatedColumn('name')
            )
            ->add((new Filter('name', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->translator->trans('Search name', [], 'Admin.Shopparameters.Help'),
                    ],
                ])
                ->setAssociatedColumn('name')
            )
            ->add((new Filter('reduction', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->translator->trans('Search discount', [], 'Admin.Shopparameters.Help'),
                    ],
                ])
                ->setAssociatedColumn('reduction')
            )
            ->add((new Filter('members', NumberType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->translator->trans('Search members', [], 'Admin.Shopparameters.Help'),
                    ],
                ])
                ->setAssociatedColumn('members')
            )
            ->add((new Filter('show_prices', ChoiceType::class))
                ->setTypeOptions([
                    'required' => false,
                    'choices' => [
                        $this->trans('Yes', [], 'Admin.Global') => 1,
                        $this->trans('No', [], 'Admin.Global') => 0,
                    ],
                ])
                ->setAssociatedColumn('show_prices')
            )
            ->add((new Filter('date_add', DateRangeType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('date_add')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setAssociatedColumn('actions')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add((new SimpleGridAction('common_refresh_list'))
                ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                ->setIcon('refresh')
            )
            ->add((new SimpleGridAction('common_show_query'))
                ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                ->setIcon('code')
            )
            ->add((new SimpleGridAction('common_export_sql_manager'))
                ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                ->setIcon('storage')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('delete_selection'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_languages_index',
                    'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                ])
            )
        ;
    }
}
