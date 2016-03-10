<?php

class Smartwave_Porto_Model_System_Config_Source_Setting_Category_Layout
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'one_column', 'label' => '1 Column'),
            array('value' => 'two_column_left', 'label' => '2 Columns with Left Sidebar'),
            array('value' => 'two_column_right', 'label' => '2 Columns with Right Sidebar'),
            array('value' => 'three_column', 'label' => '3 Columns')
        );
    }
}