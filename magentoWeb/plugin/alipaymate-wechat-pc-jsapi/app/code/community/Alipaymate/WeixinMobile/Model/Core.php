<?php

class Alipaymate_WeixinMobile_Model_Core
{
	const MODULE_PAYMENT_WEIXIN_PAY_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
	const MODULE_PAYMENT_WEIXIN_OAUTH2_CODE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
	const MODULE_PAYMENT_WEIXIN_OAUTH2_OPENID_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
	const MODULE_PAYMENT_WEIXIN_PAY_ORDER_QUERY_URL = 'https://api.mch.weixin.qq.com/pay/orderquery';
	private $_curl_timeout = 30;
	private $_code = null;
	private $_helper;
	private $_config = array('notify_url' => '', 'trade_type' => '', 'appid' => '', 'secret' => '', 'mch_id' => '', 'key' => '', 'openid' => '', 'device_info' => '', 'nonce_str' => '', 'sign' => '', 'time_start' => '', 'time_expire' => '', 'limit_pay' => '', 'spbill_create_ip' => '','weixin_pay_finish_url' => '');
	private $_bizparam = array('out_trade_no' => '', 'body' => '', 'detail' => '', 'attach' => '', 'fee_type' => '', 'total_fee' => '', 'goods_tag' => '', 'product_id' => '');
	public function __construct()
	{
		$this->getHelper();
	}
  public function is_weixin()
  {
      if (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
          return true;
      }
      return false;
  }
	public function getHelper()
	{
		if (!$this->_helper) {
			$this->_helper = Mage::helper('weixinmobile');
		}
		return $this->_helper;
	}
	public function setConfig($config)
	{
		foreach ($config as $name => $value) {
			if (array_key_exists($name, $this->_config)) {
				$this->_config[$name] = trim($value);
			}
		}
	}
	public function getConfig()
	{
		return $this->_config;
	}
	public function setBizParams($params)
	{
		foreach ($params as $name => $value) {
			if (array_key_exists($name, $this->_bizparam)) {
				$this->_bizparam[$name] = trim($value);
			}
		}
	}
	public function verifyNotification($data)
	{
		$sign = $data['sign'];
		$mysign = $this->makeSign($data);
		$this->_helper->log('verifyNotification', $data);
		$this->_helper->log('verifyNotification', array('sign' => $sign, 'mysign' => $mysign));
		if ($sign == $mysign) {
			return true;
		}
		return false;
	}
	public function makeSign($params, $url_encode = false)
	{
		$str = '';
		ksort($params);
		$filter = array('sign', 'key');
		foreach ($params as $k => $v) {
			if (in_array($k, $filter) || $v == '') {
				continue;
			}
			if ($url_encode) {
				$v = urlencode($v);
			}
			$str .= $k . '=' . $v . '&';
		}
		$str .= 'key=' . $this->_config['key'];
		$sign = strtoupper(md5($str));
		return $sign;
	}
	public function convertArrayToXml($arr)
	{
		$xml = '<xml>';
		foreach ($arr as $key => $val) {
			$val = trim($val);
			if (empty($val) || $key == 'key') {
				continue;
			}
			if (is_numeric($val)) {
				$xml .= '<' . $key . '>' . $val . '</' . $key . '>';
			} else {
				$xml .= '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
			}
			$xml .= '<' . $key . '>' . $val . '</' . $key . '>';
		}
		$xml .= '</xml>';
		return trim($xml);
	}
	public function convertXmlToArray($xml)
	{
		libxml_disable_entity_loader(true);
		$arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		libxml_disable_entity_loader(false);
		return $arr;
	}
	private function postXmlCurl($xml, $url, $cert = false, $timeout = 30)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$this->_helper->log('postXmlCurl url', $url);
		$this->_helper->log('postXmlCurl xml', $xml);
		$data = curl_exec($ch);
		$this->_helper->log('postXmlCurl response', $data);
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
	public function ip()
	{
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		return $ip;
	}
	public function createNoncestr($length = 32)
	{
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$str = '';
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}
	public function postXmlSSLCurl($xml, $url, $timeout = 30)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$data = curl_exec($ch);
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			return false;
		}
	}
	public function createOauthUrlForCode($redirect_url = '')
	{
		if (empty($redirect_url)) {
			if (isset($_GET['repay']) && $_GET['repay'] == 1) {
				$orderId = $this->_bizparam['out_trade_no'];
				$redirect_url = $this->getJsApiCallUrl() . '?orderId=' . $orderId;
				$redirect_url = urlencode($redirect_url);
			} else {
				$redirect_url = urlencode($this->getJsApiCallUrl());
			}
		}
		$params['appid'] = $this->_config['appid'];
		$params['redirect_uri'] = $redirect_url;
		$params['response_type'] = 'code';
		$params['scope'] = 'snsapi_base';
		$params['state'] = 'STATE#wechat_redirect';
		$this->_helper->log('createOauthUrlForCode params', $params);
		$str = $this->toUrlParams($params, false);
		$url = self::MODULE_PAYMENT_WEIXIN_OAUTH2_CODE_URL . $str;
		$this->_helper->log('createOauthUrlForCode url', $url);
		return $url;
	}
	public function getJsApiCallUrl()
	{
		return Mage::getUrl('weixinmobile/processing/redirect', array('_secure' => true));
	}
	public function toUrlParams($params, $url_encode = false)
	{
		$str = '';
		ksort($params);
		foreach ($params as $k => $v) {
			if ($url_encode) {
				$v = urlencode($v);
			}
			$str .= $k . '=' . $v . '&';
		}
		if (strlen($str) > 0) {
			$str = substr($str, 0, strlen($str) - 1);
		}
		return $str;
	}
	public function getJsApiParameters()
	{
		$prepay_id = $this->getPrePayId();
		$params['appId'] = $this->_config['appid'];
		$params['timeStamp'] = '' . time();
		$params['nonceStr'] = $this->createNoncestr();
		$params['package'] = 'prepay_id=' . $prepay_id;
		$params['signType'] = 'MD5';
		$params['paySign'] = $this->makeSign($params);
		$this->_helper->log('getJsApiParameters (params)', $params);
		$json = json_encode($params);
		$this->_helper->log('getJsApiParameters (json params)', $json);
		return $json;
	}
	public function getPrePayId()
	{
		$url = self::MODULE_PAYMENT_WEIXIN_PAY_URL;
		$requestXML = $this->createPrePayRequestXML();
		$this->_helper->log('getPrePayId (requestXML)', $requestXML);
		$responseXml = $this->postXmlCurl($requestXML, $url, false);
		$this->_helper->log('getPrePayId (responseXml)', $responseXml);
		$responseArr = $this->convertXmlToArray($responseXml);
		$this->_helper->log('getPrePayId (responseArr)', $responseArr);
		if (isset($responseArr['prepay_id'])) {
			return $responseArr['prepay_id'];
		}
		return false;
	}
	public function createPrePayRequestXML()
	{
		$data = array_merge($this->_config, $this->_bizparam);
		$params = array();
		$params['appid'] = $data['appid'];
		$params['mch_id'] = $data['mch_id'];
		$params['spbill_create_ip'] = $this->ip();
		$params['nonce_str'] = $this->createNoncestr();
		$params['out_trade_no'] = $data['out_trade_no'];
		$params['body'] = $data['body'];
		$params['total_fee'] = $data['total_fee'];
		$params['notify_url'] = $data['notify_url'];
		$params['trade_type'] = $data['trade_type'];
		$params['openid'] = $data['openid'];
		$params['sign'] = $this->makeSign($params);
		$xml = $this->convertArrayToXml($params);
		return $xml;
	}
	public function getOpenid()
	{
		if (!isset($_GET['code'])) {
			$url = $this->createOauthUrlForCode();
			header("Location: {$url}");
			die;
		}
		$code = $_GET['code'];
		$openid = $this->GetOpenidFromMp($code);
		$this->_config['openid'] = $openid;
		return $openid;
	}
	public function GetOpenidFromMp($code)
	{
		$url = $this->createOauthUrlForOpenid($code);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$res = curl_exec($ch);
		curl_close($ch);
		$this->_helper->log('GetOpenidFromMp curl results', $res);
		$openid = '';
		if ($data = json_decode($res, true)) {
			if (isset($data['openid'])) {
				$openid = $data['openid'];
			}
		}
		$this->_helper->log('GetOpenidFromMp curl results(array)', $data);
		$this->_helper->log('GetOpenidFromMp curl openid', $openid);
		if (empty($openid)) {
			return false;
		}
		return $openid;
	}
	public function createOauthUrlForOpenid($code)
	{
		$params['appid'] = $this->_config['appid'];
		$params['secret'] = $this->_config['secret'];
		$params['code'] = $code;
		$params['grant_type'] = 'authorization_code';
		$this->_helper->log('createOauthUrlForOpenid params', $params);
		$str = $this->toUrlParams($params, false);
		$url = self::MODULE_PAYMENT_WEIXIN_OAUTH2_OPENID_URL . $str;
		$this->_helper->log('createOauthUrlForOpenid url', $url);
		return $url;
	}
	public function paid($transaction_id, $out_trade_no = '')
	{
		$transaction_id = trim($transaction_id);
		$out_trade_no = trim($out_trade_no);
		if (empty($transaction_id) && empty($out_trade_no)) {
			return false;
		}
		$data = array();
		$data['appid'] = $this->_config['appid'];
		$data['mch_id'] = $this->_config['mch_id'];
		$data['nonce_str'] = $this->createNoncestr();
		if (!empty($transaction_id)) {
			$data['transaction_id'] = $transaction_id;
		} else {
			$data['out_trade_no'] = $out_trade_no;
		}
		ksort($data);
		$data['sign'] = $this->makeSign($data);
		$this->_helper->log('queryOrder (request data)', $data);
		$url = self::MODULE_PAYMENT_WEIXIN_PAY_ORDER_QUERY_URL;
		$requestXML = $this->convertArrayToXml($data);
		$this->_helper->log('queryOrder (request xml)', $requestXML);
		$responseXml = $this->postXmlCurl($requestXML, $url, false);
		$this->_helper->log('queryOrder (responseXml)', $responseXml);
		$responseArr = $this->convertXmlToArray($responseXml);
		$this->_helper->log('queryOrder (responseArr)', $responseArr);
		if (isset($responseArr['trade_state']) && $responseArr['trade_state'] == 'SUCCESS') {
			return true;
		}
		return false;
	}
}
