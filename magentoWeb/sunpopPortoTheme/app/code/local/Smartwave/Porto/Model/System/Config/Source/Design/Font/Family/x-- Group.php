<?php

class Smartwave_Porto_Model_System_Config_Source_Design_Font_Family_Group
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'Arial, "Helvetica Neue", Helvetica, sans-serif',
				  'label' => Mage::helper('porto')->__('Arial, "Helvetica Neue", Helvetica, sans-serif')),
			array('value' => 'Georgia, serif',
				  'label' => Mage::helper('porto')->__('Georgia, serif')),
			array('value' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
				  'label' => Mage::helper('porto')->__('"Lucida Sans Unicode", "Lucida Grande", sans-serif')),
			array('value' => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
				  'label' => Mage::helper('porto')->__('"Palatino Linotype", "Book Antiqua", Palatino, serif')),
			array('value' => 'Tahoma, Geneva, sans-serif',
				  'label' => Mage::helper('porto')->__('Tahoma, Geneva, sans-serif')),
			array('value' => '"Trebuchet MS", Helvetica, sans-serif',
				  'label' => Mage::helper('porto')->__('"Trebuchet MS", Helvetica, sans-serif')),
			array('value' => 'Verdana, Geneva, sans-serif',
				  'label' => Mage::helper('porto')->__('Verdana, Geneva, sans-serif')),
			array('value' => '',
				  'label' => Mage::helper('porto')->__('Other...')),
        );
    }
}