<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */


/**
 * @author Amasty
 */
class Amasty_Fpccrawler_Model_Source_Sources extends Mage_Core_Model_Config_Data
{
    public function toOptionArray($addEmpty = true)
    {
        $sitemapFile = Mage::getBaseDir() . '/sitemap.xml';
        if (file_exists($sitemapFile)) {
            $options[] = array(
                'label' => 'Sitemap XML',
                'value' => 'sitemap'
            );
        }
        if (Mage::helper('core')->isModuleEnabled('Amasty_Fpc')) {
            $options[] = array(
                'label' => 'Amasty FPC module built-in table',
                'value' => 'fpc'
            );
        }

        $options[] = array(
            'label' => 'Magento built-in log table',
            'value' => 'magento'
        );
        $options[] = array(
            'label' => 'Text file with one link per line',
            'value' => 'file'
        );

        return $options;
    }

    public function toArray(array $arrAttributes = array())
    {
        $sitemapFile = Mage::getBaseDir() . '/sitemap.xml';
        if (file_exists($sitemapFile)) {
            $options[] = array(
                'label' => 'Sitemap XML',
                'value' => 'sitemap'
            );
        }
        if (Mage::helper('core')->isModuleEnabled('Amasty_Fpc')) {
            $options[] = array(
                'label' => 'Amasty FPC module built-in table',
                'value' => 'fpc'
            );
        }

        $options[] = array(
            'label' => 'Magento built-in log table',
            'value' => 'magento'
        );
        $options[] = array(
            'label' => 'Text file with one link per line',
            'value' => 'file'
        );

        return $options;
    }
}