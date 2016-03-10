<?php

class Smartwave_Megamenu_Model_Category_Attribute_Source_Block_Yesno
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
                array('value' => '0',        'label' => 'No'),
                array('value' => '1',        'label' => 'Yes')
            );
        }
        return $this->_options;
    }
}
