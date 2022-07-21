<?php

namespace Savvii\CheckPerformanceRows;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class MinifySettingsRows
 * 
 * @package Savvii\CheckPerformanceRows
 */
class MinifySettingsRows extends AbstractRow
{
    protected $storeManager;

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
     * @return array 
     */
    public function getRow()
    {
        $results = [];
        $configs = array(
            'js' => array('title' => 'Minify JavaScript Files', 'path' => 'dev/js/minify_files'),
            'css' => array('title' => 'Minify CSS Files', 'path' => 'dev/css/minify_files'),
            'html' => array('title' => 'Minify HTML', 'path' => 'dev/template/minify_html')
        );
        $mapping = array(0 => 'Disabled', 1 => 'Enabled');

        foreach ($configs as $config) {
            $message = 'All stores have value "Enabled"';
            $status = $this->formatStatus('STATUS_OK');
            $wrongConfigValues = $this->getWrongConfigValues($config['path'], 1, $mapping);
            if (count($wrongConfigValues) > 0) {
                $message = implode("\n", $wrongConfigValues);
                $status = $this->formatStatus('STATUS_PROBLEM');
            }

            $results[] = [
                $config['title'],
                $status,
                $message,
                'Enabled'
            ];
        }

        return $results;
    }
}
