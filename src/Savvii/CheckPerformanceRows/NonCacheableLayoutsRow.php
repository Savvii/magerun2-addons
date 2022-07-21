<?php

namespace Savvii\CheckPerformanceRows;

use LogicException;
use Magento\Theme\Model\ResourceModel\Theme\Collection as ThemeCollection;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

use UnexpectedValueException;

/**
 * Class NonCacheableLayoutsRow 
 * 
 * @package Savvii\CheckPerformanceRows
 */
class NonCacheableLayoutsRow extends AbstractRow
{
    protected $themeCollection;

    protected $files;


    /**
     * @param ThemeCollection $themeCollection 
     * @param Files $files 
     * @param ScopeConfigInterface $scopeConfig 
     * @param StoreManagerInterface $storeManager 
     * 
     * @return void 
     */
    public function __construct(ThemeCollection $themeCollection, Files $files, ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager)
    {
        $this->themeCollection = $themeCollection;
        $this->files = $files;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return (string|void)[] 
     * @throws LogicException 
     * @throws UnexpectedValueException 
     */
    public function getRow()
    {
        $elementsToInclude = array('catalog', 'cms');
        $usedThemes = $this->getConfigValuesByPath('design/theme/theme_id');
        if (count($usedThemes) < 1) {
            return array(
                'Non Cacheable Layouts',
                $this->formatStatus('STATUS_UNKNOWN'),
                'Unable to fetch themes from config',
                'None'
            );
        }
        $usedThemePaths = [];
        foreach ($this->themeCollection as $theme) {
            if (in_array($theme->getId(), $usedThemes)) {
                array_push($usedThemePaths, $theme->getThemePath());
                $currentParent = $theme->getParentTheme();
                while ($currentParent) {
                    array_push($usedThemePaths, $currentParent->getThemePath());
                    $currentParent = $currentParent->getParentTheme();
                }
            }
        }

        $files = array();
        foreach (array_unique($usedThemePaths) as $usedThemePath) {
            $files = array_merge(
                $files,
                $this->files->getLayoutFiles(
                    array('area' => 'frontend', 'theme_path' => $usedThemePath),
                    false
                )
            );
        }

        $badNonCacheAbleElements = array();
        foreach ($files as $file) {
            $xml = simplexml_load_file($file, "SimpleXMLElement", LIBXML_NOERROR |  LIBXML_ERR_NONE);
            if ($xml) {
                $elements = $xml->xpath('//*[@cacheable="false"]');
                foreach ($elements as $element) {
                    $needsLogging = false;

                    if (
                        preg_match('(' . implode('|', $elementsToInclude) . ')', $file) === 1
                        || preg_match(
                            '(' . implode('|', $elementsToInclude) . ')',
                            $element['name']
                        ) === 1
                    ) {
                        $needsLogging = true;
                    }

                    if ($needsLogging && strpos($element['name'], 'compare') === false) {
                        array_push($badNonCacheAbleElements, $element['name']);
                    }
                }
            }
        }

        return array(
            'Non Cacheable Layouts',
            count($badNonCacheAbleElements) > 0 ? $this->formatStatus('STATUS_PROBLEM')
                : $this->formatStatus('STATUS_OK'),
            count($badNonCacheAbleElements) > 0 ? implode("\n", array_unique($badNonCacheAbleElements)) : 'None',
            'None'
        );
    }
}
