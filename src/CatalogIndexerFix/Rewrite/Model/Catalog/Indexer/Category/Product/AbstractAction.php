<?php

namespace Silarn\CatalogIndexerFix\Rewrite\Model\Catalog\Indexer\Category\Product;

use Silarn\CatalogIndexerFix\Rewrite\Framework\DB\Query\BatchIteratorInterface as BatchIteratorInterface;
use Silarn\CatalogIndexerFix\Rewrite\Framework\DB\Query\Generator as QueryGenerator;
use Magento\Framework\App\ResourceConnection;

abstract class AbstractAction extends \Magento\Catalog\Model\Indexer\Category\Product\AbstractAction
{
    /**
     * @var QueryGenerator
     */
    private $queryGenerator;

    /**
     * @param ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $config
     * @param QueryGenerator $queryGenerator
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $config,
        QueryGenerator $queryGenerator = null
    ) {
        parent::__construct($resource, $storeManager, $config);
        $this->queryGenerator = $queryGenerator ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(QueryGenerator::class);
    }

    /**
     * Return selects cut by min and max
     *
     * @param \Magento\Framework\DB\Select $select
     * @param string $field
     * @param int $range
     * @return \Magento\Framework\DB\Select[]
     */
    protected function prepareSelectsByRange(
        \Magento\Framework\DB\Select $select,
        $field,
        $range = self::RANGE_CATEGORY_STEP
    ) {
        if($this->isRangingNeeded()) {
            $iterator = $this->queryGenerator->generate(
                $field,
                $select,
                $range,
                BatchIteratorInterface::NON_UNIQUE_FIELD_ITERATOR
            );

            $queries = [];
            foreach ($iterator as $query) {
                $queries[] = $query;
            }
            return $queries;
        }
        return [$select];
    }
}