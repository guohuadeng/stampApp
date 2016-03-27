<?php

class Alipaymate_Weixinlogin_Model_Login extends Mage_Core_Model_Abstract
{
    protected $_code  = 'weixinlogin';
    protected $_formBlockType = 'weixinlogin/form';

    public function prepareConfig()
    {
        $config = array(
            '_input_charset'    => 'utf-8',
            'service'           => 'weixin.auth.authorize',
            'target_service'    => 'user.auth.quick.login',
            'sign_type'         => 'MD5',
            'appid'             => $this->getConfigData('customer/weixinlogin/app_id'),
            'secret'            => $this->getConfigData('customer/weixinlogin/app_secret'),
            'appid2'            => $this->getConfigData('customer/weixinlogin/app_id2'),
            'secret2'           => $this->getConfigData('customer/weixinlogin/app_secret2'),
            'return_url'        => $this->getReturnURL(),
        );

        return $config;
    }

	public function getRedirectURL()
	{
        return Mage::getUrl('weixinlogin/processing/redirect/', array('_secure' => true));
	}

	public function getReturnURL()
	{
        return Mage::getUrl('weixinlogin/processing/return/', array('_secure' => true));
	}

	private function getConfigData($key) {
	    return Mage::getStoreConfig($key);
	}
}