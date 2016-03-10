<?php

class Smartwave_Megamenu_Model_Category_Attribute_Source_Type_Float
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{    
    /**
     * Get list of available block column proportions
     */
    public function getAllOptions()
    {
        if (!$this->_options)
        {
            $this->_options = array(
                array('value' => '',     'label' => 'Default'),
                array('value' => 'right',        'label' => 'Right')
            );
        }
        return $this->_options;
    }
}
