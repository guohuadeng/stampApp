<?php

class Alipaymate_Weixin_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('alipaymate/weixin/form.phtml');
    }

    public function showLogo() {
        return Mage::getStoreConfig('payment/weixin/show_logo');
    }
}