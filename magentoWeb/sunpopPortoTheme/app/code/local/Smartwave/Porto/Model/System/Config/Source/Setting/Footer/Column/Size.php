<?php

class Smartwave_Porto_Model_System_Config_Source_Setting_Footer_Column_Size
{
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' => Mage::helper('porto')->__('1/12')),
            array('value' => '2', 'label' => Mage::helper('porto')->__('2/12')),
            array('value' => '3', 'label' => Mage::helper('porto')->__('3/12')),
            array('value' => '4', 'label' => Mage::helper('porto')->__('4/12')),
            array('value' => '5', 'label' => Mage::helper('porto')->__('5/12')),
            array('value' => '6', 'label' => Mage::helper('porto')->__('6/12')),
            array('value' => '7', 'label' => Mage::helper('porto')->__('7/12')),
            array('value' => '8', 'label' => Mage::helper('porto')->__('8/12')),
            array('value' => '9', 'label' => Mage::helper('porto')->__('9/12')),
            array('value' => '10', 'label' => Mage::helper('porto')->__('10/12')),
            array('value' => '11', 'label' => Mage::helper('porto')->__('11/12')),
            array('value' => '12', 'label' => Mage::helper('porto')->__('12/12'))
        );
    }
}