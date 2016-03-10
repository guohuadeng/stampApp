<?php

class Smartwave_Porto_Model_System_Config_Source_Setting_Product_Tab_Style
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'horizontal', 'label' => Mage::helper('porto')->__('Horizontal')),
            array('value' => 'vertical', 'label' => Mage::helper('porto')->__('Vertical')),
            array('value' => 'accordion', 'label' => Mage::helper('porto')->__('Accordion'))
        );
    }
}