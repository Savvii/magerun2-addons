<?php

namespace Savvii\CheckPerformanceRows;

use Magento\PageCache\Model\Config as CacheConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;


/**
 * Class FullPageCacheApplicationRow 
 * 
 * @package Savvii\CheckPerformanceRows
 */
class FullPageCacheApplicationRow extends AbstractRow
{
    /**
     * @param ScopeConfigInterface $scopeConfig 
     * @param StoreManagerInterface $storeManager 
     * 
     * @return void 
     */
    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * 
     * @return (string|void)[] 
     */
    public function getRow()
    {
        $mapping = [CacheConfig::VARNISH => 'Varnish', CacheConfig::BUILT_IN => 'Built in'];
        $wrongConfigValues = $this->getWrongConfigValues('system/full_page_cache/caching_application', CacheConfig::VARNISH, $mapping);
        if (count($wrongConfigValues) > 0) {
            return array(
                'Full Page Cache',
                $this->formatStatus('STATUS_PROBLEM'),
                implode("\n", $wrongConfigValues),
                'Varnish'
            );
        }

        return array(
            'Full Page Cache',
            $this->formatStatus('STATUS_OK'),
            'All stores have value "Varnish"',
            'Varnish'
        );
    }
}
