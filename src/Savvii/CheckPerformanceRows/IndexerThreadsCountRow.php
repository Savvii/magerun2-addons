<?php

namespace Savvii\CheckPerformanceRows;

use Magento\Framework\App\DeploymentConfig;

/**
 * Class IndexerThreadsCountRow 
 * 
 * @package Savvii\CheckPerformanceRows
 */
class IndexerThreadsCountRow extends AbstractRow
{

    protected $deploymentConfig;

    /**
     * @param DeploymentConfig $deploymentConfig 
     * 
     * @return void 
     */
    public function __construct(DeploymentConfig $deploymentConfig)
    {
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * @return (string|void)[] 
     */
    public function getRow()
    {
        $status = $this->formatStatus('STATUS_OK');
        $threadsCount = $this->deploymentConfig->get('MAGE_INDEXER_THREADS_COUNT');
        if(!$threadsCount) {
            $threadsCount = getenv('MAGE_INDEXER_THREADS_COUNT');
        }
        if ($threadsCount < 2) {
            $status = $this->formatStatus('STATUS_PROBLEM');
        }

        return array(
            'Indexer Threads Count',
            $status,
            $threadsCount ? $threadsCount : 0,
            '>=2'
        );
    }
}
