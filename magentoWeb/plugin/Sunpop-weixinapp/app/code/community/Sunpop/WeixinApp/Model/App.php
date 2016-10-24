<?php

class Sunpop_WeixinApp_Model_App
{
    private $_config = array();

    public function payment($config)
    {
        $this->_config = $config;

        $prepay_id = $this->generatePrepayId($config['appid'], $config['mch_id']);
        // re-sign it
        $response = array(
            'appid' => $config['appid'],
            'partnerid' => $config['mch_id'],
            'prepayid' => $prepay_id,
            'package' => 'Sign=WXPay',
            'noncestr' => $this->generateNonce(),
            'timestamp' => time(),
        );

        $response['sign'] = $this->calculateSign($response, $config['key']);

        return $response;
    }

    /**
     * Generate a nonce string.
     *
     * @link https://pay.weixin.qq.com/wiki/doc/api/app.php?chapter=4_3
     */
    private function generateNonce()
    {
        return md5(uniqid('', true));
    }

    /**
     * Get a sign string from array using app key.
     *
     * @link https://pay.weixin.qq.com/wiki/doc/api/app.php?chapter=4_3
     */
    private function calculateSign($arr, $key)
    {
        ksort($arr);

        $buff = '';
        foreach ($arr as $k => $v) {
            if ($k != 'sign' && $k != 'key' && $v != '' && !is_array($v)) {
                $buff .= $k.'='.$v.'&';
            }
        }

        $buff = trim($buff, '&');

        return strtoupper(md5($buff.'&key='.$key));
    }

    /**
     * Get xml from array.
     */
    private function getXMLFromArray($arr)
    {
        $xml = '<xml>';
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= sprintf('<%s>%s</%s>', $key, $val, $key);
            } else {
                $xml .= sprintf('<%s><![CDATA[%s]]></%s>', $key, $val, $key);
            }
        }

        $xml .= '</xml>';

        return $xml;
    }

    /**
     * Generate a prepay id.
     *
     * @link https://pay.weixin.qq.com/wiki/doc/api/app.php?chapter=9_1
     */
    private function generatePrepayId($app_id, $mch_id)
    {
        $params = array(
            'appid' => $app_id,
            'mch_id' => $mch_id,
            'nonce_str' => $this->generateNonce(),
            'body' => $this->_config['body'],
            'out_trade_no' => $this->_config['order_id'],
            'total_fee' => $this->_config['total_fee'],
            'spbill_create_ip' => $this->_config['spbill_create_ip'],
            'notify_url' => $this->_config['notify_url'],
            'trade_type' => $this->_config['trade_type']
        );

        //add openid 如果是 jsapi,是否要写代码？
        if ($this->_config['trade_type'] == 'JSAPI')
            $params['openid'] = $this->_config['openid'];
        // add sign
        $params['sign'] = $this->calculateSign($params, $this->_config['key']);

        // create xml
        $xml = $this->getXMLFromArray($params);

        // send request
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://api.mch.weixin.qq.com/pay/unifiedorder',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-Type: text/xml'),
            CURLOPT_POSTFIELDS => $xml,
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        // get the prepay id from response
        $xml = simplexml_load_string($result);

        return (string) $xml->prepay_id;
    }
}
