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

namespace PrestaShop\PrestaShop\Core\Grid\Factory;

use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Grid;
use PrestaShopBundle\Service\Hook\HookDispatcher;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GridFactory is responsible for creating grid from it's definition
 */
final class GridFactory implements GridFactoryInterface
{
    /**
     * @var HookDispatcher
     */
    private $dispatcher;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param HookDispatcher $dispatcher
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        HookDispatcher $dispatcher,
        FormFactoryInterface $formFactory
    ) {
        $this->dispatcher = $dispatcher;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromDefinition(GridDefinitionInterface $gridDefinition, Request $request) {
        // Execute hook to allow developers to modify/extend grid definition
        // For exmaple add new columns, row actions & etc.
        $this->dispatcher->dispatchForParameters('modifyGridDefinition', [
            'grid_definition' => $gridDefinition,
        ]);

        $filtersForm = $this->buildGridFilterForm($gridDefinition);
        $filtersForm->handleRequest($request);

        $grid = new Grid($gridDefinition, $filtersForm);

        return $grid;
    }

    /**
     * Builds filters form for grid
     *
     * @param GridDefinitionInterface $grid
     *
     * @return FormInterface
     */
    private function buildGridFilterForm(GridDefinitionInterface $grid)
    {
        $formBuilder = $this->formFactory->createNamedBuilder($grid->getIdentifier());

        foreach ($grid->getColumns() as $column) {
            if ($formType = $column->getFilterFormType()) {
                $options = $column->getFilterFormTypeOptions();

                if (!isset($options['required'])) {
                    $options['required'] = false;
                }

                $formBuilder->add(
                    $column->getIdentifier(),
                    $formType,
                    $options
                );
            }
        }

        $form = $formBuilder
            ->setData([])
            ->getForm()
        ;

        return $form;
    }
}