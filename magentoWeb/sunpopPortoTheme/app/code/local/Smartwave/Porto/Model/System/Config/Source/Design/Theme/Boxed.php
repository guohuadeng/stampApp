<?php

class Smartwave_Porto_Model_System_Config_Source_Design_Theme_Boxed
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'wide', 'label' => Mage::helper('porto')->__('Wide (Default)')),
			array('value' => 'boxed', 'label' => Mage::helper('porto')->__('Boxed'))
        );
    }
}