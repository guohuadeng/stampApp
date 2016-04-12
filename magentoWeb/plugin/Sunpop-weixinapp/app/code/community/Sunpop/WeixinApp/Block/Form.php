<?php

class Sunpop_WeixinApp_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sunpop/weixinapp/form.phtml');
    }
    
    public function showLogo() {
        return Mage::getStoreConfig('payment/weixinapp/show_logo');
    }    
}