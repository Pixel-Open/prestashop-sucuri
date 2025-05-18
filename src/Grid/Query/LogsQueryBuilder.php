<?php
/**
 * Copyright (C) 2025 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pixel\Module\Sucuri\Grid\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class LogsQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const ALIAS = 'main_table';

    private const PRIMARY = 'id';

    private Context $shopContext;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param Context $shopContext
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        Context $shopContext
    ) {
        $this->shopContext = $shopContext;

        parent::__construct($connection, $dbPrefix);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQuery();
        $qb->select('*')
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if (!$filterValue) {
                continue;
            }
            if ('id' === $filterName) {
                $qb->andWhere(self::ALIAS . '.id = :filterName');
                $qb->setParameter('filterName', $filterValue);

                continue;
            }
            if ('full_date' === $filterName) {
                if (isset($filterValue['from'])) {
                    $qb->andWhere(self::ALIAS . '.full_date >= :date_from');
                    $qb->setParameter('date_from', sprintf('%s 0:0:0', $filterValue['from']));
                }

                if (isset($filterValue['to'])) {
                    $qb->andWhere(self::ALIAS . '.full_date <= :date_to');
                    $qb->setParameter('date_to', sprintf('%s 23:59:59', $filterValue['to']));
                }

                continue;
            }
            if (is_array($filterValue)) {
                continue;
            }

            $qb->andWhere(self::ALIAS . '.' . $filterName . ' LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }

        return $qb;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQuery();
        $qb->select('COUNT(' . self::ALIAS . '.' . self::PRIMARY . ')');

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    private function getBaseQuery(): QueryBuilder
    {
        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'sucuri_log', self::ALIAS)
            ->where('shop_id = :context_shop_id OR shop_id = 0')
            ->setParameter(
                'context_shop_id',
                !$this->shopContext->isShopContext() ? 0 : $this->shopContext->getContextShopID()
            );
    }
}
