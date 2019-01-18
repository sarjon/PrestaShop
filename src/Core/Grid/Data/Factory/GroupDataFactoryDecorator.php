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

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Decorates raw Groups data that is retrieved from database
 */
final class GroupDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $rawGroupsDataFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param GridDataFactoryInterface $rawGroupsDataFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        GridDataFactoryInterface $rawGroupsDataFactory,
        TranslatorInterface $translator
    ) {
        $this->rawGroupsDataFactory = $rawGroupsDataFactory;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $rawData = $this->rawGroupsDataFactory->getData($searchCriteria);

        return new GridData(
            $this->getModifiedRecords($rawData->getRecords()),
            $rawData->getRecordsTotal(),
            $rawData->getQuery()
        );
    }

    /**
     * @param RecordCollectionInterface $rawRecords
     *
     * @return RecordCollectionInterface
     */
    private function getModifiedRecords(RecordCollectionInterface $rawRecords)
    {
        $records = [];

        foreach ($rawRecords as $rawRecord) {
            $rawRecord['reduction'] = sprintf('%s %%', $rawRecord['reduction']);
            $rawRecord['show_prices'] = $rawRecord['show_prices'] ?
                $this->translator->trans('Yes', [], 'Admin.Global') :
                $this->translator->trans('No', [], 'Admin.Global');

            $records[] = $rawRecord;
        }

        return new RecordCollection($records);
    }
}
