<?php

class Smartwave_Megamenu_Model_Category_Attribute_Source_Label_CategoryLabel
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_options;
    
    /**
     * Get list of existing category labels
     */
    public function getAllOptions()
    {
        $h = Mage::helper('megamenu');
        
        if (!$this->_options)
        {    
            $this->_options[] =
                    array('value' => '', 'label' => " ");
                    
            if ($tmp = trim($h->getConfig('category_labels/label1')))
            {
                $this->_options[] =
                    array('value' => 'label1', 'label' => $tmp);
            }
            if ($tmp = trim($h->getConfig('category_labels/label2')))
            {
                $this->_options[] =
                    array('value' => 'label2', 'label' => $tmp);
            }
        }
        return $this->_options;
    }
}
