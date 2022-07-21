<?php

namespace Savvii\CheckPerformanceRows;

/**
 * Class AbstractRow 
 * 
 * @package Savvii\CheckPerformanceRows
 */
abstract class AbstractRow
{

    protected $format;

    protected $storeManager;

    protected $scopeConfig;

    /**
     * @param mixed $format 
     * 
     * @return $this 
     */
    public function setInputFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param mixed $status 
     * 
     * @return string|void 
     */
    protected function formatStatus($status)
    {
        if ($status === 'STATUS_OK') {
            if ($this->format !== null) {
                return 'ok';
            }

            return '<info>ok</info>';
        }

        if ($status === 'STATUS_PROBLEM') {
            if ($this->format !== null) {
                return 'problem';
            }

            return '<error>problem</error>';
        }

        if ($status === 'STATUS_UNKNOWN') {
            if ($this->format !== null) {
                return 'unknown';
            }

            return '<warning>unknown</warning>';
        }
    }

    protected function getConfigValuesByPath($path)
    {
        $result = [];
        if ($this->storeManager && $this->scopeConfig) {
            $stores = $this->storeManager->getStores();
            foreach ($stores as $store) {
                $result[] = $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode());
            }
        }
        return $result;
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    protected function getWrongConfigValues($path, $recommended, $mapping)
    {
        $result = [];
        if ($this->storeManager && $this->scopeConfig) {
            $stores = $this->storeManager->getStores();
            $allStoresFail = true;
            foreach ($stores as $store) {
                $configValue = $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getCode());
                if ($configValue != $recommended) {
                    $result[] = 'Store ' . $store->getId() . ' has value "' . $mapping[$configValue] . '"';
                } else {
                    $allStoresFail = false;
                }
            }
            if ($allStoresFail) {
                $result = ['All stores have value "' . $mapping[$configValue] . '"'];
            }
        } else {
            $result = ['Unable to initialize'];
        }
        return $result;
    }

    /**
     * @param $bytes
     *
     * @return mixed
     */
    protected function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
