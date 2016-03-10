<?php

class Smartwave_Porto_Model_System_Config_Source_Design_Border_Style
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'none', 'label' => Mage::helper('porto')->__('none')),
			array('value' => 'hidden', 'label' => Mage::helper('porto')->__('hidden')),
            array('value' => 'dotted', 'label' => Mage::helper('porto')->__('dotted')),
            array('value' => 'dashed', 'label' => Mage::helper('porto')->__('dashed')),
            array('value' => 'solid', 'label' => Mage::helper('porto')->__('solid')),
            array('value' => 'double', 'label' => Mage::helper('porto')->__('double')),
            array('value' => 'groove', 'label' => Mage::helper('porto')->__('groove')),
            array('value' => 'ridge', 'label' => Mage::helper('porto')->__('ridge')),
            array('value' => 'inset', 'label' => Mage::helper('porto')->__('inset')),
            array('value' => 'outset', 'label' => Mage::helper('porto')->__('outset')),
            array('value' => 'initial', 'label' => Mage::helper('porto')->__('initial')),
            array('value' => 'inherit', 'label' => Mage::helper('porto')->__('inherit'))
        );
    }
}