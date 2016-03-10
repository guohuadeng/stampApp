<?php

class Smartwave_Porto_Model_System_Config_Source_Setting_Footer_Column_Middle
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('porto')->__('Do not show')),
            array('value' => 'custom', 'label' => Mage::helper('porto')->__('Custom Block')),
            array('value' => 'newsletter', 'label' => Mage::helper('porto')->__('Newsletter Subscribe')),
            array('value' => 'facebook', 'label' => Mage::helper('porto')->__('Facebook Widget')),
            array('value' => 'flickr', 'label' => Mage::helper('porto')->__('Flickr Stream'))
        );
    }
}