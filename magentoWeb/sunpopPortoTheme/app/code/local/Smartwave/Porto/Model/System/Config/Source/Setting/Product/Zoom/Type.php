<?php

class Smartwave_Porto_Model_System_Config_Source_Setting_Product_Zoom_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('porto')->__('Inner Zoom')),
            array('value' => '1', 'label' => Mage::helper('porto')->__('Right Side Zoom'))
        );
    }
}