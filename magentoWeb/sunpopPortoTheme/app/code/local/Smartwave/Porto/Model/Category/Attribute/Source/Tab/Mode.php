<?php

class Smartwave_Porto_Model_Category_Attribute_Source_Tab_Mode
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
                array('value' => '',        'label' => 'From Parent'),
                array('value' => 'yes',       'label' => 'Yes'),
                array('value' => 'no',       'label' => 'No')
            );
        }
        return $this->_options;
    }
}
