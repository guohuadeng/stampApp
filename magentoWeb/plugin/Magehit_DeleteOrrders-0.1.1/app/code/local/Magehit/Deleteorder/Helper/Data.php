<?php
class Magehit_Deleteorder_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected function _initConfig()
    {
        return Mage::getSingleton('deleteorder/config');
    }

    public function isEnabled(){
        return $this->_initConfig()->isEnabled();
    }
}