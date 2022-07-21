<?php

namespace Savvii\CheckPerformanceRows;

use Magento\Indexer\Model\Indexer\Collection as IndexerCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AsyncIndexingRow 
 * 
 * @package Savvii\CheckPerformanceRows
 */
class AsyncIndexingRow extends AbstractRow
{
    protected $indexerCollection;

    /**
     * @param ScopeConfigInterface $scopeConfig 
     * @param StoreManagerInterface $storeManager 
     * @param IndexerCollection $indexerCollection 
     * 
     * @return void 
     */
    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager, IndexerCollection $indexerCollection)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->indexerCollection = $indexerCollection;
    }

    /**
     * @return (string|void)[] 
     */
    public function getRow()
    {
        $mapping = [0 => 'Disabled', 1 => 'Enabled'];
        $wrongConfigValues = $this->getWrongConfigValues('dev/grid/async_indexing', 1, $mapping);
        if (count($wrongConfigValues) > 0) {
            return array(
                'Asynchronous Indexing',
                $this->formatStatus('STATUS_PROBLEM'),
                implode("\n", $wrongConfigValues),
                'All indexers async'
            );
        }

        $disabledIndexersMessage = '';
        foreach ($this->indexerCollection->getItems() as $indexer) {
            if (!$indexer->isScheduled()) {
                $disabledIndexersMessage .= $indexer->getTitle() . ' Index mode is not "Scheduled"' . "\n";
            }
        }

        return array(
            'Asynchronous Indexing',
            ($disabledIndexersMessage == '' ? $this->formatStatus('STATUS_OK') : $this->formatStatus('STATUS_PROBLEM')),
            ($disabledIndexersMessage == '' ? 'All stores have value "Enabled"' : $disabledIndexersMessage),
            'All indexers have mode "scheduled"'
        );
    }
}
