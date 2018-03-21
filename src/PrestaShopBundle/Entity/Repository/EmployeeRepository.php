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

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;

class EmployeeRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tablePrefix;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string Shop association table
     */
    private $tableShop;

    public function __construct(Connection $connection, $tablePrefix)
    {
        //@todo: check context for shop & language
        $this->connection = $connection;
        $this->tablePrefix = $tablePrefix;

        $this->table = $tablePrefix.'employee';
        $this->tableShop = $this->table.'_shop';
    }

    /**
     * Get all employees with profile information
     *
     * @param int $languageId
     * @param int $shopId
     *
     * @return array
     */
    public function getAllWithProfileInformation($languageId, $shopId)
    {
        $profileLangTable = $this->tablePrefix.'profile_lang';
        $queryBuilder = $this->connection->createQueryBuilder();

        $condition = $queryBuilder->expr()->andX(
            $queryBuilder->expr()->eq('pl.id_lang', (int) $languageId),
            $queryBuilder->expr()->eq('es.id_shop', (int) $shopId)
        );

        $qb = $queryBuilder
            ->select('e.*, pl.name as profile_name')
            ->from($this->table, 'e')
            ->innerJoin('e', $profileLangTable, 'pl', 'e.id_profile = pl.id_profile')
            ->leftJoin('e', $this->tableShop, 'es', 'e.id_employee = es.id_employee')
            ->where($condition)
        ;

        $statement = $qb->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}