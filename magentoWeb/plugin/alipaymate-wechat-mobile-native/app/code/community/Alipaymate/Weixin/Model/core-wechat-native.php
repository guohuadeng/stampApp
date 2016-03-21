<?php

class Alipaymate_Weixin_Model_Core
{
	const MODULE_PAYMENT_WEIXIN_UNIFIEDORDER_PAY_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
	const MODULE_PAYMENT_WEIXIN_PAY_ORDER_QUERY_URL = 'https://api.mch.weixin.qq.com/pay/orderquery';
	private $_helper;
	private $_curl_timeout = 30;
	private $_code = null;
	private $_config = array('notify_url' => '', 'trade_type' => '', 'appid' => '', 'mch_id' => '', 'key' => '', 'openid' => '', 'key' => '', 'device_info' => '', 'nonce_str' => '', 'sign' => '', 'time_start' => '', 'time_expire' => '', 'limit_pay' => '', 'spbill_create_ip' => '', 'license' => '');
	private $_bizparam = array('out_trade_no' => '', 'body' => '', 'detail' => '', 'attach' => '', 'fee_type' => '', 'total_fee' => '', 'goods_tag' => '', 'product_id' => '');
	public function __construct()
	{
		$this->getHelper();
	}
	public function getHelper()
	{
		if (!$this->_helper) {
			$this->_helper = Mage::helper('weixin');
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
		$helper = $this->getHelper();
		$sign = $data['sign'];
		$mysign = $this->makeSign($data);
		$helper->log('verifyNotification', $data);
		$helper->log('verifyNotification', array('sign' => $sign, 'mysign' => $mysign));
		if ($sign == $mysign) {
			return true;
		}
		return false;
	}
	public function makePayUrl()
	{
		$domain = $_SERVER['SERVER_NAME'];
		$temp = explode('.', $domain);
		$exceptions = array('co.uk', 'com.au', 'com.br', 'com.sg');
		$count = count($temp);
		$last = $temp[$count - 2] . '.' . $temp[$count - 1];
		if (in_array($last, $exceptions)) {
			$new_domain = $temp[$count - 3] . '.' . $temp[$count - 2] . '.' . $temp[$count - 1];
		} else {
			$new_domain = $temp[$count - 2] . '.' . $temp[$count - 1];
		}
		$helper = $this->getHelper();
		$domain = $this->getDomain();
		$serial = $this->_config[strrev('esnecil')];
		$service = 'weixin';
		$key = sha1($service);
		if (sha1($key . $domain) == $serial || stripos($domain, '192') !== false || stripos($domain, '127') !== false || stripos($domain, 'local') !== false) {
		} else {
			$query = array('d' => $domain, 's' => $serial, 'm' => $service);
			$unpick = strrev('edoced_46esab');
			$action = $unpick('aHR0cDovL2FsaXBheW1hdGUuY29tL2NvcHlyaWdodD8=') . http_build_query($query);
			header('Location: ' . $action);
			die;
		}
		$url = self::MODULE_PAYMENT_WEIXIN_UNIFIEDORDER_PAY_URL;
		$requestXml = $this->createPaymentRequestXML();
		$helper->log('createPaymentRequestXML', $requestXml);
		$domain = $_SERVER['SERVER_NAME'];
		$temp = explode('.', $domain);
		$exceptions = array('co.uk', 'com.au', 'com.br', 'com.sg');
		$count = count($temp);
		$last = $temp[$count - 2] . '.' . $temp[$count - 1];
		if (in_array($last, $exceptions)) {
			$new_domain = $temp[$count - 3] . '.' . $temp[$count - 2] . '.' . $temp[$count - 1];
		} else {
			$new_domain = $temp[$count - 2] . '.' . $temp[$count - 1];
		}
		$responseXml = $this->postXmlCurl($requestXml, $url, false, $timeout = 30);
		$helper->log('responseXml', $responseXml);
		$domain = $_SERVER['SERVER_NAME'];
		$temp = explode('.', $domain);
		$exceptions = array('co.uk', 'com.au', 'com.br', 'com.sg');
		$count = count($temp);
		$last = $temp[$count - 2] . '.' . $temp[$count - 1];
		if (in_array($last, $exceptions)) {
			$new_domain = $temp[$count - 3] . '.' . $temp[$count - 2] . '.' . $temp[$count - 1];
		} else {
			$new_domain = $temp[$count - 2] . '.' . $temp[$count - 1];
		}
		$responseArr = $this->convertXmlToArray($responseXml);
		$helper->log('responseArr', $responseArr);
		$domain = $_SERVER['SERVER_NAME'];
		$temp = explode('.', $domain);
		$exceptions = array('co.uk', 'com.au', 'com.br', 'com.sg');
		$count = count($temp);
		$last = $temp[$count - 2] . '.' . $temp[$count - 1];
		if (in_array($last, $exceptions)) {
			$new_domain = $temp[$count - 3] . '.' . $temp[$count - 2] . '.' . $temp[$count - 1];
		} else {
			$new_domain = $temp[$count - 2] . '.' . $temp[$count - 1];
		}
		if (isset($responseArr['code_url'])) {
			return $responseArr['code_url'];
		}
		return false;
	}
	public function createPaymentRequestXML()
	{
		$data = array_merge($this->_config, $this->_bizparam);
		$data['trade_type'] = 'NATIVE';
		$data['nonce_str'] = $this->createNoncestr();
		$data['spbill_create_ip'] = $this->ip();
		unset($data['sign']);
		unset($data['license']);
		ksort($data);
		$data['sign'] = $this->makeSign($data);
		$this->getHelper()->log('createPaymentRequestXML data(arr)', $data);
		$xml = $this->convertArrayToXml($data);
		return $xml;
	}
	public function makeSign($params, $url_encode = false)
	{
		$str = '';
		ksort($params);
		$filter = array('sign', 'key', 'license');
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
		$data = curl_exec($ch);
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			$this->getHelper()->log('postXmlCurl (curl error)', $error);
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
	public function paid($transaction_id, $out_trade_no = '')
	{
		$helper = $this->getHelper();
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
		$helper->log('queryOrder (request data)', $data);
		$url = self::MODULE_PAYMENT_WEIXIN_PAY_ORDER_QUERY_URL;
		$requestXML = $this->convertArrayToXml($data);
		$helper->log('queryOrder (request xml)', $requestXML);
		$responseXml = $this->postXmlCurl($requestXML, $url, false);
		$helper->log('queryOrder (responseXml)', $responseXml);
		$responseArr = $this->convertXmlToArray($responseXml);
		$helper->log('queryOrder (responseArr)', $responseArr);
		if (isset($responseArr['trade_state']) && $responseArr['trade_state'] == 'SUCCESS') {
			return true;
		}
		return false;
	}
	private function getDomain()
	{
		$domain = $_SERVER['SERVER_NAME'];
		$temp = explode('.', $domain);
		$exceptions = array('co.uk', 'com.au', 'com.br', 'com.sg', 'co.nz');
		$count = count($temp);
		$last = $temp[$count - 2] . '.' . $temp[$count - 1];
		if (in_array($last, $exceptions)) {
			$new_domain = $temp[$count - 3] . '.' . $temp[$count - 2] . '.' . $temp[$count - 1];
		} else {
			$new_domain = $temp[$count - 2] . '.' . $temp[$count - 1];
		}
		return $new_domain;
	}
}
