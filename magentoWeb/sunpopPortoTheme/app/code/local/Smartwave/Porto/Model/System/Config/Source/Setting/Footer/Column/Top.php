<?php

class Smartwave_Porto_Model_System_Config_Source_Setting_Footer_Column_Top
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('porto')->__('Do not show')),
            array('value' => 'custom', 'label' => Mage::helper('porto')->__('Custom Block')),
            array('value' => 'twitter', 'label' => Mage::helper('porto')->__('Twitter Widget'))
        );
    }
}