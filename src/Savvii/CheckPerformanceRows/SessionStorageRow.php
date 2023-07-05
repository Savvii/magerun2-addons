<?php

namespace Savvii\CheckPerformanceRows;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\Config;

/**
 * Class SessionStorageRow 
 * 
 * @package Savvii\CheckPerformanceRows
 */
class SessionStorageRow extends AbstractRow
{
    /**
     * @var DeploymentConfig
     */
    private DeploymentConfig $deploymentConfig;

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
     * @return array 
     */
    public function getRow()
    {
        $defaultSaveHandler = ini_get('session.save_handler')
            ?:
            SaveHandlerInterface::DEFAULT_HANDLER;
        $saveHandler = $this->deploymentConfig->get(
            Config::PARAM_SESSION_SAVE_METHOD,
            $defaultSaveHandler
        );
        $recommended = array('redis', 'memcache', 'memcached');

        return array(
            'Session Storage',
            in_array($saveHandler, $recommended) ? $this->formatStatus('STATUS_OK')
                : $this->formatStatus('STATUS_PROBLEM'),
            $saveHandler,
            'redis, memcache or memcached',
        );
    }
}
