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

namespace PrestaShop\PrestaShop\Core\Grid\Query\Builder;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds query for retrieving customer groups data
 */
final class GroupQueryBuilder implements DoctrineQueryBuilderInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var string
     */
    private $contextLangId;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param int $contextLangId
     * @param int[] $contextShopIds
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        $contextLangId,
        array $contextShopIds,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->contextLangId = $contextLangId;
        $this->contextShopIds = $contextShopIds;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getGroupQueryBuilder()
            ->select('g.*, gl.*')
            ->addSelect('('. $this->getCustomersCountInGroupQueryBuilder()->getSQL() . ') AS nb')
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
        ;

        $this->applyFilters($qb, $searchCriteria->getFilters());
        $this->applySorting($qb, $searchCriteria);

        $this->searchCriteriaApplicator->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getGroupQueryBuilder()
            ->select('COUNT(g.id_group)')
            ->addSelect('('. $this->getCustomersCountInGroupQueryBuilder()->getSQL() . ') AS nb')
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
        ;

        $this->applyFilters($qb, $searchCriteria->getFilters());

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    private function getGroupQueryBuilder()
    {
        return $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'group', 'g')
            ->leftJoin(
                'g',
                $this->dbPrefix . 'group_lang',
                'gl',
                'gl.id_group = g.id_group AND gl.id_lang = :context_lang_id'
            )
            ->setParameter('context_lang_id', $this->contextLangId)
        ;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $queryBuilder, array $filters)
    {
        $allowedFilters = [
            'id_group',
            'name',
            'reduction',
            'nb',
            'show_prices',
            'date_add',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ('id_group' === $filterName) {
                $queryBuilder->andWhere('g.id_group = :' . $filterName);
                $queryBuilder->setParameter($filterName, $filterValue);

                continue;
            }

            if ('name' === $filterName) {
                $queryBuilder->andWhere('gl.name LIKE :name');
                $queryBuilder->setParameter('name', '%' . $filterValue . '%');

                continue;
            }

            if ('reduction' === $filterName) {
                $queryBuilder->andWhere('g.reduction LIKE :reduction');
                $queryBuilder->setParameter('reduction', '%' . $filterValue . '%');

                continue;
            }

            if ('nb' === $filterName) {
                $queryBuilder->andHaving('nb = :members_count');
                $queryBuilder->setParameter('members_count', $filterValue);

                continue;
            }

            if ('show_prices' === $filterName) {
                $queryBuilder->andWhere('g.show_prices = :show_prices');
                $queryBuilder->setParameter('show_prices', $filterValue);

                continue;
            }

            if ('date_add' === $filterName) {
                if (isset($filterValue['from'], $filterValue['to'])) {
                    $queryBuilder->andWhere('g.date_add >= :date_from AND g.date_add <= :date_to');
                    $queryBuilder->setParameter('date_from', sprintf('%s 0:0:0', $filterValue['from']));
                    $queryBuilder->setParameter('date_to', sprintf('%s 23:59:59', $filterValue['to']));
                } elseif (isset($filterValue['from'])) {
                    $queryBuilder->andWhere('g.date_add >= :date_from');
                    $queryBuilder->setParameter('date_from', sprintf('%s 0:0:0', $filterValue['from']));
                } elseif (isset($filterValue['to'])) {
                    $queryBuilder->andWhere('g.date_add <= :date_to');
                    $queryBuilder->setParameter('date_to', sprintf('%s 23:59:59', $filterValue['to']));
                }

                continue;
            }

            $queryBuilder->andWhere("`$filterName` LIKE :$filterName");
            $queryBuilder->setParameter($filterName, '%' . $filterValue . '%');
        }
    }

    /**
     * Applies sorting to Group query builder
     *
     * @param QueryBuilder $queryBuilder
     * @param SearchCriteriaInterface $criteria
     */
    private function applySorting(QueryBuilder $queryBuilder, SearchCriteriaInterface $criteria)
    {
        switch ($criteria->getOrderBy()) {
            case 'id_group':
                $orderBy = 'g.id_group';
                break;
            default:
                $orderBy = $criteria->getOrderBy();
                break;
        }

        $queryBuilder->orderBy($orderBy, $criteria->getOrderWay());
    }

    /**
     * @return QueryBuilder
     */
    private function getCustomersCountInGroupQueryBuilder()
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT(cg.id_customer) ')
            ->from($this->dbPrefix . 'customer_group', 'cg')
            ->leftJoin('cg', $this->dbPrefix . 'customer', 'c', 'c.id_customer = cg.id_customer')
            ->andWhere('c.deleted != 1')
            ->andWhere('c.id_shop IN (:context_shop_ids)')
            ->andWhere('cg.id_group = g.id_group')
        ;
    }
}
