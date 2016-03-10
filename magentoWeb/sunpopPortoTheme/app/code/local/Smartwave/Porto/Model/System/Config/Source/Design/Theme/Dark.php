<?php

class Smartwave_Porto_Model_System_Config_Source_Design_Theme_Dark
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'light', 'label' => Mage::helper('porto')->__('Light (Default)')),
			array('value' => 'dark', 'label' => Mage::helper('porto')->__('Dark'))
        );
    }
}