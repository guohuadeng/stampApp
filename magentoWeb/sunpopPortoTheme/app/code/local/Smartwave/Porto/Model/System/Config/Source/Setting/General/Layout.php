<?php

class Smartwave_Porto_Model_System_Config_Source_Setting_General_Layout
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('porto')->__('1170px')),
            array('value' => '1024', 'label' => Mage::helper('porto')->__('1024px')),
            array('value' => '1280', 'label' => Mage::helper('porto')->__('1280px')),
            array('value' => 'full_width', 'label' => Mage::helper('porto')->__('Full Width'))
        );
    }
}