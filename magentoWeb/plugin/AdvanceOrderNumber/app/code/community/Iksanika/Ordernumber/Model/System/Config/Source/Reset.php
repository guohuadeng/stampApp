<?php

class Iksanika_Ordernumber_Model_System_Config_Source_Reset
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => '',
                'label' => Mage::helper('ordernumber')->__('Never'),
            ),
            array(
                'value' => 'Y',
                'label' => Mage::helper('ordernumber')->__('Each Year'),
            ),
            array(
                'value' => 'Y-m',
                'label' => Mage::helper('ordernumber')->__('Each Month'),
            ),
            array(
                'value' => 'Y-m-d',
                'label' => Mage::helper('ordernumber')->__('Each Day'),
            ),
        );
        return $options;
    }
}