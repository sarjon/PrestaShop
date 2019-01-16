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
     * @param Connection $connection
     * @param string $dbPrefix
     * @param $contextLangId
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        $contextLangId,
        array $contextShopIds
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->contextLangId = $contextLangId;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $customersCountInGroupQuery = $this->connection->createQueryBuilder()
            ->select('COUNT(cg.id_customer) ')
            ->from($this->dbPrefix . 'customer_group', 'cg')
            ->leftJoin('cg', $this->dbPrefix . 'customer', 'c', 'c.id_customer = cg.id_customer')
            ->andWhere('c.deleted != 1')
            ->andWhere('c.id_shop IN (:context_shop_ids)')
            ->andWhere('cg.id_group = g.id_group')
            ->getSQL()
        ;

        $qb = $this->getGroupQueryBuilder()
            ->select('g.*, gl.*')
            ->addSelect($customersCountInGroupQuery)
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getGroupQueryBuilder()
            ->select('COUNT(g.id_group)')
        ;
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
}
