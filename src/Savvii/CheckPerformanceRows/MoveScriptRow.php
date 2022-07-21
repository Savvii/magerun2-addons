<?php

namespace Savvii\CheckPerformanceRows;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class MoveScriptRow extends AbstractRow
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
     * @return (string|void)[] 
     */
    public function getRow()
    {
        $mapping = [0 => 'Disabled', 1 => 'Enabled'];
        $wrongConfigValues = $this->getWrongConfigValues('dev/js/move_script_to_bottom', 1, $mapping);

        if (count($wrongConfigValues) > 0) {
            return array(
                'Move Script To Bottom',
                $this->formatStatus('STATUS_PROBLEM'),
                implode("\n", $wrongConfigValues),
                'Enabled'
            );
        }


        return array(
            'Move Script To Bottom',
            $this->formatStatus('STATUS_OK'),
            'All stores have value "Enabled"',
            'Enabled'
        );
    }
}
