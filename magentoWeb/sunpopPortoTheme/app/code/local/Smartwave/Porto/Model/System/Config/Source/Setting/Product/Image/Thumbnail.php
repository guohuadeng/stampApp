<?php

class Smartwave_Porto_Model_System_Config_Source_Setting_Product_Image_Thumbnail
{
    public function toOptionArray()
    {
        return array(
            array('value' => '', 'label' => Mage::helper('porto')->__('Horizontal')),
            array('value' => 'vertical', 'label' => Mage::helper('porto')->__('Vertical'))
        );
    }
}