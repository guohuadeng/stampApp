<?php

class Smartwave_QuickView_Helper_Data extends Mage_Core_Helper_Abstract
{
       
    public function getConfig($option) {
        return Mage::getStoreConfig('quickview/general/' . $option);
    }
    
    public function isEnabled() {
        return $this->getConfig('enableview');
    }
    
    //get popup settings
    public function getDialogWidth() {
        $width = $this->getConfig('dialog_width');
        if ($width) {
            return $width;
        } else {
            return 685;     //Default value
        }
    }
    
    public function getDialogHeight() {
        $height = $this->getConfig('dialog_height');
        if ($height) {
            return $height;
        } else {
            return 700;
        }
    }
}