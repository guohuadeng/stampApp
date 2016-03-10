<?php

class Smartwave_Porto_Model_System_Config_Source_Design_Font_Size_Basic
{
    public function toOptionArray()
    {
		return array(
            array('value' => '0',    'label' => Mage::helper('porto')->__('Default')),
            array('value' => '10px',    'label' => Mage::helper('porto')->__('10 px')),
            array('value' => '11px',    'label' => Mage::helper('porto')->__('11 px')),
			array('value' => '12px',	'label' => Mage::helper('porto')->__('12 px')),
			array('value' => '13px',	'label' => Mage::helper('porto')->__('13 px')),
            array('value' => '14px',    'label' => Mage::helper('porto')->__('14 px')),
            array('value' => '15px',    'label' => Mage::helper('porto')->__('15 px')),
            array('value' => '16px',    'label' => Mage::helper('porto')->__('16 px')),
            array('value' => '17px',    'label' => Mage::helper('porto')->__('17 px')),
            array('value' => '18px',	'label' => Mage::helper('porto')->__('18 px'))
        );
    }
}