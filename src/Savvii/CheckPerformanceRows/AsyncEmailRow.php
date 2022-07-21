<?php

namespace Savvii\CheckPerformanceRows;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Sales\Model\ResourceModel\Sale\Collection as SalesCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AsyncEmailRow
 * 
 * @package Savvii\CheckPerformanceRows
 */
class AsyncEmailRow extends AbstractRow
{
    protected $productMetadata;

    protected $salesCollection;

    /**
     * @param ScopeConfigInterface $scopeConfig 
     * @param StoreManagerInterface $storeManager 
     * @param ProductMetadataInterface $productMetadata 
     * @param SalesCollection $salesCollection 
     * 
     * @return void 
     */
    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager, ProductMetadataInterface $productMetadata, SalesCollection $salesCollection)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->productMetadata = $productMetadata;
        $this->salesCollection = $salesCollection;
    }

    /**
     * @return (string|void)[] 
     */
    public function getRow()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $status = $this->formatStatus('STATUS_OK');
        $message = 'All stores have value "Enabled"';
        $mapping = [0 => 'Disabled', 1 => 'Enabled'];

        $wrongConfigValues = $this->getWrongConfigValues('sales_email/general/async_sending', 1, $mapping);
        if (count($wrongConfigValues) > 0) {
            $status = $this->formatStatus('STATUS_PROBLEM');
            $message = implode("\n", $wrongConfigValues);
            if (version_compare($magentoVersion, '2.4.2', '<')) {
                $this->salesCollection->addFieldToFilter('send_email', ['eq' => 1]);
                $this->salesCollection->addFieldToFilter('email_sent', ['null' => true]);
                if ($this->salesCollection->count() > 0) {
                    $message .= "\n" . 'Warning: found ' . $this->salesCollection->count() . ' old emails. Do not enable before cleanup (MC-39521).';
                }
            }
        }

        return array(
            'Asynchronous Sending Of Sales Emails',
            $status,
            $message,
            'Enabled'
        );
    }
}
