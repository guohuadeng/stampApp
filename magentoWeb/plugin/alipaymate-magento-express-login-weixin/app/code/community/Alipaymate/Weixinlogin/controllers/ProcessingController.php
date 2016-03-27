<?php

class Alipaymate_Weixinlogin_ProcessingController extends Mage_Core_Controller_Front_Action
{
    protected function _getHelper()
    {
        return Mage::helper('weixinlogin');
    }

    /**
     * Redirect to Wechat
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('core/session');

        try {
            $url = $this->_getRefererUrl();
            $session->setBeforeWeixinAuthUrl($url);

            $this->getResponse()->setBody($this->getLayout()->createBlock('weixinlogin/redirect')->toHtml());
            return;
        } catch (Exception $e) {
            Mage::logException($e);
            $session->addNotice($e->getMessage());
        }
    }


    /**
     * Weixin Return
     */
    public function returnAction()
    {
        $request = $this->getRequest()->getQuery();

        $_helper = $this->_getHelper()->setReturnLog();
        $_helper->log('weixinlogin-return', $request);

        try {
            $login   = Mage::getModel('weixinlogin/login');
            $config  = $login->prepareConfig();

            $weixin = Mage::getModel('weixinlogin/core');
            $weixin->setConfig($config);

            $info = $weixin->getUserInfo();

            if (! isset($info['unionid'])) {
            	Mage::throwException($_helper->__('Sorry, Wechat login failed!'));
            }

            /*
             * customer login
             */
            $info['inside_weixin'] = 0;

            if ($weixin->is_weixin()) {
                $info['inside_weixin'] = 1;
            }

            $identifierHelper = Mage::helper('weixinlogin/identifiers');
			
			$collection = Mage::getModel('weixinlogin/identifiers')
				->getCollection()
				->addFieldToFilter('unionid', $info['unionid']);
			
			$params = "?unionid=".$info['unionid'];
			
			if($collection->getSize()){
				$customer = $identifierHelper->getCustomer($info['unionid']);
				if (!$customer || ! $customer->getId()) {
					$url = Mage::getUrl('customer/account/create').$params;
				}else{ 
					$currentcustomer = Mage::getModel("customer/customer")->load($customer->getId());
					Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($currentcustomer);
					$url = Mage::getSingleton('core/session')->getBeforeWeixinAuthUrl();	
					
				}
			}else{
				$identifierHelper->saveLoginWeixin($info);
				$url = Mage::getUrl('customer/account/create').$params;
			}	
            
        } catch (Exception $e) {
            $_helper->log('weixinlogin-return (error)', $e->getMessage());
            Mage::getSingleton('core/session')->addNotice($_helper->__('Sorry, WeChat login failed!'));
        }
		echo '<script type="text/javascript">top.location.href="' .$url. '";</script>';
		exit();
        
    }
	
	public function newloginAction(){
        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
       // $this->_initLayoutMessages('customer/session');
       // $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
	}
}
