<?php
class Magehit_Deleteorder_Model_Config extends Mage_Core_Model_Abstract{
    const XML_GENERAL_ENABLE = 'deleteorder/general/enable';

    public function isEnabled(){
        return $this->getConfig(self::XML_GENERAL_ENABLE);
    }
    
    public function getConfig($name = null){
        if(!$name) return null;
        return Mage::getStoreConfig($name);
    }
}