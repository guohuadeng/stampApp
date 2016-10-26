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
            //在微信公众号里使用stampwx，对于以非mg架构的页面，要知道来访问页面，用php自有的referer功能
            $weixin = Mage::getModel('weixinlogin/core');
            if ($weixin->is_weixin()) {
              $url = $_SERVER['HTTP_REFERER'];
            } else  {
              $url = $this->_getRefererUrl();
            }
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
        $request = $this->getRequest()->getParams();

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

			$params = "?unionid=".$info['unionid'] . "&openid=".$info['openid'] . "&nickname=".$info['nickname']
			    . "&sex=".$info['sex'] . "&city=".$info['city'] . "&province=".$info['province']
			    . "&country=".$info['country'] . "&headimgurl=".urlencode($info['headimgurl']);

			if($collection->getSize()){ //有该粉丝信息的情况下
				$customer = $identifierHelper->getCustomer($info['unionid']);
				if (!$customer || ! $customer->getId()) {
          //在微信公众号里使用stampwx，跳转到用户注册界面
          if ($weixin->is_weixin() && !empty($config['app_register2'])) {
            $url = $config['app_register2'].$params;
          } else  {
            $url = Mage::getUrl('customer/account/create').$params;
            }
				}else{
					$currentcustomer = Mage::getModel("customer/customer")->load($customer->getId());
					Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($currentcustomer);
					$url = Mage::getSingleton('core/session')->getBeforeWeixinAuthUrl();
				}
			}else{  //没有该粉丝信息，存信息并注册
				$identifierHelper->saveLoginWeixin($info);
        //在微信公众号里使用stampwx，跳转到用户注册界面
        if ($weixin->is_weixin() && !empty($config['app_register2'])) {
          $url = $config['app_register2'].$params;
        } else  {
				  $url = Mage::getUrl('customer/account/create').$params;
				  }
			}

        } catch (Exception $e) {
            $_helper->log('weixinlogin-return (error)', $e->getMessage());
            Mage::getSingleton('core/session')->addNotice($_helper->__('Sorry, WeChat login failed!'));
        }
		echo '<script type="text/javascript">top.location.href="' .$url. '";</script>';
		//echo $url;
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
