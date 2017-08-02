<?php
namespace Silarn\CatalogIndexerFix\Rewrite\Framework\DB\Adapter\Pdo;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DB\Query\Generator as QueryGenerator;

class Mysql extends \Magento\Framework\DB\Adapter\Pdo\Mysql
{
    /**
     * @var QueryGenerator
     */
    private $queryGenerator;

    /**
     * Get insert queries in array for insert by range with step parameter
     *
     * @param string $rangeField
     * @param \Magento\Framework\DB\Select $select
     * @param int $stepCount
     * @return \Magento\Framework\DB\Select[]
     * @throws LocalizedException
     * @deprecated
     */
    public function selectsByRange($rangeField, \Magento\Framework\DB\Select $select, $stepCount = 100)
    {
        $iterator = $this->getQueryGenerator()->generate($rangeField, $select, $stepCount);
        $queries = [];
        foreach ($iterator as $query) {
            $queries[] = $query;
        }
        return $queries;
    }

    /**
     * Get an array of select queries using the batching strategy
     *
     * Depending on the $batchStrategy parameter chooses a strategy. This strategy will be used to create
     * an array of select queries. By default method use $batchStrategy parameter:
     * \Magento\Framework\DB\Query\BatchIteratorFactory::UNIQUE_FIELD_ITERATOR.
     * This parameter means that values of $rangeField have relationship
     * one-to-one.
     * If values of $rangeField is non-unique and have relationship one-to-many,
     * than must be used next $batchStrategy parameter:
     * \Magento\Framework\DB\Query\BatchIteratorFactory::NON_UNIQUE_FIELD_ITERATOR.
     *
     * @see BatchIteratorFactory
     * @param string $rangeField - Field which is used for the range mechanism in select
     * @param Select $select
     * @param int $batchSize - Determines on how many parts will be divided
     * the number of values in the select.
     * @param string $batchStrategy - It determines which strategy is chosen
     * @return \Magento\Framework\DB\Select[]
     * @throws LocalizedException Throws if incorrect "FROM" part in \Select exists
     * @deprecated This is a temporary solution which is made due to the fact that we
     *             can't change method selectsByRange() in version 2.1 due to a backwards incompatibility.
     *             In 2.2 version need to use original method selectsByRange() with additional parameter.
     */
    public function selectsByRangeStrategy(
        $rangeField,
        \Magento\Framework\DB\Select $select,
        $batchSize = 100,
        $batchStrategy = \Silarn\CatalogIndexerFix\Rewrite\Framework\DB\Query\BatchIteratorFactory::UNIQUE_FIELD_ITERATOR
    ) {
        $iterator = $this->getQueryGenerator()->generate($rangeField, $select, $batchSize, $batchStrategy);

        $queries = [];
        foreach ($iterator as $query) {
            $queries[] = $query;
        }
        return $queries;
    }

    /**
     * Get query generator
     *
     * @return QueryGenerator
     * @deprecated
     */
    private function getQueryGenerator()
    {
        if ($this->queryGenerator === null) {
            $this->queryGenerator = \Magento\Framework\App\ObjectManager::getInstance()->create(QueryGenerator::class);
        }
        return $this->queryGenerator;
    }
}