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

namespace PrestaShop\PrestaShop\Core\Grid\Column;

use PrestaShop\PrestaShop\Core\Grid\Collection\AbstractCollection;
use PrestaShop\PrestaShop\Core\Grid\Exception\ColumnNotFoundException;

/**
 * Class ColumnCollection holds collection of columns for grid
 *
 * @property Column[] $items
 */
final class ColumnCollection extends AbstractCollection implements ColumnCollectionInterface
{
    /**
     * Create new columns collection from array data
     *
     * @param array $data
     *
     * @return ColumnCollectionInterface
     */
    public static function fromArray(array $data)
    {
        $columns = new self();
        $position = 0;

        foreach ($data as $columnData) {
            $columnData['position'] = $position++;

            $column = Column::fromArray($columnData);
            $columns->add($column);
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function add(ColumnInterface $column)
    {
        $this->items[$column->getId()] = $column;
    }

    /**
     * {@inheritdoc}
     */
    public function addAfter($id, ColumnInterface $newColumn)
    {
        if (!isset($this->items[$id])) {
            throw new ColumnNotFoundException(sprintf(
                'Column with id "%s" was not found.', $id
            ));
        }

        $newColumnPosition = $this->items[$id]->getPosition() + 1;
        $newColumn->setPosition($newColumnPosition);

        $this->add($newColumn);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $columnsArray = [];
        $positions = [];

        /** @var ColumnInterface $column */
        foreach ($this->items as $key => $column) {
            $columnsArray[] = [
                'id' => $column->getId(),
                'name' => $column->getName(),
                'is_sortable' => $column->isSortable(),
                'is_filterable' => $column->isFilterable(),
                'type' => $column->getType(),
                'options' => $column->getOptions(),
            ];

            $positions[$key] = $column->getPosition();
        }

        array_multisort($positions, SORT_ASC, $columnsArray);

        return $columnsArray;
    }
}
