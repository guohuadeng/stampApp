<?php
class Smartwave_Ajaxcart_Block_Ajaxcart extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getAjaxcart()     
     { 
        if (!$this->hasData('ajaxcart')) {
            $this->setData('ajaxcart', Mage::registry('ajaxcart'));
        }
        return $this->getData('ajaxcart');
        
    }
}