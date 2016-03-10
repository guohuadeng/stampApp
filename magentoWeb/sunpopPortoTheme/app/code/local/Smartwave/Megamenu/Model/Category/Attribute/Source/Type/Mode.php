<?php

class Smartwave_Megamenu_Model_Category_Attribute_Source_Type_Mode
{    
    /**
     * Get list of available block column proportions
     */
    public function toOptionArray()
    {
        return array(            
            array('value' => 'wide',        'label' => 'Full Width'),
            array('value' => 'staticwidth',       'label' => 'Static Width'),
            array('value' => 'narrow',       'label' => 'Classic')
        );
    }
}
