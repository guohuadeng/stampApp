<?php

class Alipaymate_Weixinlogin_Model_Core
{
    const WEIXIN_OAUTH2_CODE_URL  = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
    const WEIXIN_OAUTH2_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token?';

    private $_helper = null;
    private $_curl_timeout       = 30;
    private $_code               = null;

    private $_access_token = null;
    private $_refresh_token = null;
    private $_openid = null;

    private $_device = 'pc';

    private $_config     = array(
                                 'notify_url'        => ''
                                ,'trade_type'        => ''
                                ,'appid'             => ''
                                ,'secret'            => ''
                                ,'appid2'            => ''
                                ,'secret2'           => ''
                                ,'mch_id'            => ''
                                ,'key'               => ''
                                ,'openid'            => ''
                                ,'key'               => ''
                                ,'device_info'       => ''
                                ,'nonce_str'         => ''
                                ,'sign'              => ''
                                ,'time_start'        => ''
                                ,'time_expire'       => ''
                                ,'limit_pay'         => ''
                                ,'spbill_create_ip'  => ''
                         );

    public function __construct()
    {
        if (! $this->_helper) {
            $this->_helper = Mage::helper('weixinlogin');
        }

        return $this->_helper;
    }

    public function is_weixin()
    {
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }

        return false;
    }


    public function setConfig($config)
    {
        foreach ($config as $name => $value) {
            if (array_key_exists($name, $this->_config)) {
                $this->_config[$name] = trim($value);
            }
        }
    }

    public function getWeixinUrl()
    {
        return '';
    }


    public function createRequestHtml($method = 'post', $button_title = 'submit')
    {
        $this->getCode();
    }


    public function getCode()
    {
        if (!isset($_GET['code'])) {
            $url = $this->createOauthUrlForCode();
            header("Location: $url");
            exit();
        }

        return $_GET['code'];
    }

    public function getAccessToken()
    {
        $code = $_GET['code'];

        if (empty($code)) {
            return false;
        }

        $url = $this->createOauthUrl($code);
        $result = trim(file_get_contents($url));

        $this->_helper->log('getAccessToken params', $result);

        if (empty($result)) {
            return false;
        }

        $data = json_decode($result, true);

        $this->_helper->log('getAccessToken data', $data);

        if (isset($data) && isset($data['access_token']) && isset($data['openid'])) {
            $this->_access_token  = $data['access_token'];  
            $this->_openid        = $data['openid'];
            $this->_refresh_token = isset($data['refresh_token']) ? $data['refresh_token'] : '';

            return true;
        }

        return false;
    }

    public function getUserInfo()
    {
        $this->getAccessToken();

        $params['access_token'] = $this->_access_token;
        $params['openid']       = $this->_openid;

        $url = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query($params);
        $this->_helper->log('getUserInfo url', $url);

        $result = trim(file_get_contents($url));
        $this->_helper->log('getUserInfo params', $result);

        if (empty($result)) {
            return false;
        }

        $data = json_decode($result, true);

        if (! isset($data['access_token'])) {
            $data['access_token'] = $this->_access_token;
        }

        $this->_helper->log('getUserInfo data', $data);

        return $data;
    }


    public function createOauthUrlForCode($redirect_url = '')
    {
        if (empty($redirect_url)) {
            $redirect_url = ($this->getCallbackUrl());
        }

        $params['appid']         = $this->_config['appid'];
        $params['redirect_uri']  = $redirect_url;
        $params['response_type'] = 'code';
        $params['scope']         = 'snsapi_login';
        $params['state']         =  md5($this->_config['secret'].time());

        $url = 'https://open.weixin.qq.com/connect/qrconnect?';

        if ($this->is_weixin()) {
            $params['appid'] = $this->_config['appid2'];
            $params['scope'] = 'snsapi_userinfo';
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
        }

        $url .= http_build_query($params) . '#wechat_redirect';

        $this->_helper->log('createOauthUrlForCode params', $params);
        $this->_helper->log('createOauthUrlForCode url', $url);

        return $url;
    }

    public function getCallbackUrl()
    {
        return Mage::getUrl('weixinlogin/processing/return', array('_secure' => true));
    }

    public function createOauthUrl($code)
    {
        $params['appid']     = $this->_config['appid'];
        $params['secret']    = $this->_config['secret'];
        $params['code']      = $code;
        $params['grant_type']= 'authorization_code';

        if ($this->is_weixin()) {
            $params['appid']     = $this->_config['appid2'];
            $params['secret']    = $this->_config['secret2'];
        }

        $url = self::WEIXIN_OAUTH2_TOKEN_URL . http_build_query($params);

        $this->_helper->log('createOauthUrl params', $params);
        $this->_helper->log('createOauthUrl url', $url);

        return $url;
    }

    private function postCurl($data, $url, $cert=false, $timeout=30)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_TIMEOUT,        $timeout);
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER,         FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST,           TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $data);

        $this->_helper->log('postCurl url',  $url);
        $this->_helper->log('postCurl data', $data);

        $data = curl_exec($ch);

        $this->_helper->log('postCurl response', $data);

        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return false;
        }

        return false;
    }

}
