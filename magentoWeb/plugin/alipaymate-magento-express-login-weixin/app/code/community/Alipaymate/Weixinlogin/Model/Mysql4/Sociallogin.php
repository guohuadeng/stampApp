<?php
class Alipaymate_Weixinlogin_Model_Mysql4_Sociallogin extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("weixinlogin/sociallogin", "id");
    }
}