<?php

class Smartwave_Porto_Model_System_Config_Source_Setting_Newsletter_Popup
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('porto')->__('Disable')),
            array('value' => '1', 'label' => Mage::helper('porto')->__('Enable on Only Homepage')),
            array('value' => '2', 'label' => Mage::helper('porto')->__('Enable on All Pages'))
        );
    }
}