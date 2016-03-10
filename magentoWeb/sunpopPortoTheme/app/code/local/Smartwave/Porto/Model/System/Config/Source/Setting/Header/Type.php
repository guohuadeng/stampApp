<?php

class Smartwave_Porto_Model_System_Config_Source_Setting_Header_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('porto')->__('Type 1 (Default)')),
            array('value' => '1', 'label' => Mage::helper('porto')->__('Type 2')),
            array('value' => '2', 'label' => Mage::helper('porto')->__('Type 3')),
            array('value' => '3', 'label' => Mage::helper('porto')->__('Type 4')),
            array('value' => '4', 'label' => Mage::helper('porto')->__('Type 5')),
            array('value' => '5', 'label' => Mage::helper('porto')->__('Type 6')),
            array('value' => '6', 'label' => Mage::helper('porto')->__('Type 7')),
            array('value' => '7', 'label' => Mage::helper('porto')->__('Type 8')),
            array('value' => '8', 'label' => Mage::helper('porto')->__('Type 9')),
            array('value' => '9', 'label' => Mage::helper('porto')->__('Type 10')),
            array('value' => '10', 'label' => Mage::helper('porto')->__('Type 11')),
            array('value' => '11', 'label' => Mage::helper('porto')->__('Type 12')),
            array('value' => '12', 'label' => Mage::helper('porto')->__('Type 13')),
            array('value' => '13', 'label' => Mage::helper('porto')->__('Type 14'))
        );
    }
}