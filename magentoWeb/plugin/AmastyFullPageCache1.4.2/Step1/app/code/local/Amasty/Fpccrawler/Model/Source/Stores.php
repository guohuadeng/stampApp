<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */


/**
 * @author Amasty
 */
class Amasty_Fpccrawler_Model_Source_Stores extends Mage_Core_Model_Config_Data
{
    public function toOptionArray($addEmpty = true)
    {
        $options    = array();
        $storeModel = Mage::getSingleton('adminhtml/system_store');
        /* @var $storeModel Mage_Adminhtml_Model_System_Store */
        $storeCollection = $storeModel->getStoreCollection();

        if ($addEmpty) {
            $options[] = array(
                'label' => Mage::helper('adminhtml')->__('All Store Views'),
                'value' => ''
            );
        }

        foreach ($storeCollection as $store) {
            $options[] = array(
                'label' => $store->getStoreName(),
                'value' => $store->getStoreId()
            );
        }

        return $options;
    }
}