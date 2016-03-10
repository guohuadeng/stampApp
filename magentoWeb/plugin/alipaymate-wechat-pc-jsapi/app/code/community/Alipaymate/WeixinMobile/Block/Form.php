<?php

class Alipaymate_WeixinMobile_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('alipaymate/weixinmobile/form.phtml');
    }
    
    public function showLogo() {
        return Mage::getStoreConfig('payment/weixinmobile/show_logo');
    }    
}