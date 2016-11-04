<?php

class Alipaymate_WeixinMobile_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'weixinmobile';
    protected $_formBlockType = 'weixinmobile/form';

    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canRefund               = false;

    protected $_order;


    private $_config  = array();
    private $_bizData = array();


    public function __construct()
    {
        if (!$this->isWeixinBrowser()) {
            $this->_canUseCheckout = false;
        }

        parent::__construct();
    }


    public function isWeixinBrowser()
    {
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
		if (!$this->_order) {
            $request = $_GET;

            // repay orderId in customer order list.
            if (isset($request['orderId']) && $request['orderId'] > '') {
                $orderId = $request['orderId'];
                $this->_order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
                return $this->_order;
            }

            // orderid in normal checkout
			$orderIncrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
			$this->_order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		}

		return $this->_order;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function prepareConfig()
    {
        $config = array(
            'trade_type'        => 'JSAPI',
            'mch_id'            => trim($this->getConfigData('mchid')),
            'key'               => trim($this->getConfigData('security_key')),
            'appid'             => trim($this->getConfigData('app_id')),
            'secret'            => trim($this->getConfigData('app_secret')),
            'weixin_pay_finish_url'            => trim($this->getConfigData('weixin_pay_finish_url')),
            'spbill_create_ip'  => '',
            'device_info'       => 'WEB',
            'limit_pay'         => '',
            'notify_url'        => $this->getNotifyURL(),
            'license'           => trim($this->getConfigData('license')),
        );

        $this->_config = $config;

        return $config;
    }


    public function prepareBizData()
    {
        $allow_currencies = array('CNY');

        $order_currency = $this->_getCurrency();
        $order_total    = $this->_getTotalFee();

        $currency  = '';
        $total_fee = 0.00;

        if (in_array($order_currency, $allow_currencies)) {
            $currency  = $order_currency;
            $total_fee = $order_total;
        } else { // convert to CNY
            $base_currency    = $this->getOrder()->getBaseCurrencyCode();
            $base_grand_total = $this->getOrder()->getBaseGrandTotal();

            try {
                $currency  = 'CNY';
                $total_fee = Mage::getModel('directory/currency')->load($base_currency)->convert($base_grand_total, $currency);
                $total_fee = sprintf("%.2f", $total_fee);
            } catch (Exception $e) {
                Mage::throwException(Mage::helper('weixinmobile')->__('Please install CNY currency'));
            }

        }

        $total_fee = sprintf("%.2f", $total_fee);
        $total_fee = (int)($total_fee * 100);
        $orderId = $this->getOrder()->getRealOrderId();

        $param = array(
            'out_trade_no'       => $orderId,
            'fee_type'           => 'CNY',
            'total_fee'          => $total_fee,
            'body'               => $this->_getBody(),
            'product_id'         => $orderId,
        );

        $this->_bizData = $param;

        return $param;
    }


    protected function _getTotalFee()
    {
        $total = $this->getOrder()->getGrandTotal();

        $total = sprintf("%.2f", $total);
        return $total;
    }

    protected function _getCurrency()
    {
        $currency = $this->getOrder()->getOrderCurrencyCode();
        return $currency;
    }

    protected function _getSubject()
    {
        return 'Order: ' . $this->getOrder()->getRealOrderId();
    }

    protected function _getBody() {
        return 'Order: ' . $this->getOrder()->getRealOrderId();
    }


    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('weixinmobile/processing/redirect', array('_secure' => true));
    }

	public function getReturnURL($orderId)
	{
        return Mage::getUrl('weixinmobile/processing/return/', array('_secure' => true,'orderId' => $orderId));
	}

	public function getNotifyURL()
	{
		return Mage::getUrl('weixinmobile/processing/notify/', array('_secure' => true));
	}

	public function getPaidURL($orderId)  //微信公众号内支付用不上
	{
        return Mage::getUrl('weixinmobile/processing/paid/', array('_secure' => true,'orderId' => $orderId));
	}

	public function getCode()
	{
	    return $this->_code;
	}

	public function canRepay($order)
	{
        try {
            if ($this->getConfigData('enable_repay') <= 0) {
                return false;
            }

            if ($this->getConfigData('active') <= 0) {
                return false;
            }

            if (!$order->canInvoice()) {
                return false;
            }

            if (!$this->isWeixinBrowser()) {//微信公众号支付必须到公众号内才显示
                return false;
            }
            //微信扫码的不能换成微信公众号的支付，反之亦然，APP内的是另外的商户号，不受影响
            if ($order->getPayment()->getMethodInstance()->getCode() == 'weixin') {
                return false;
            }

        } catch (Exception $e) {
            return false;
        }

        return true;
	}

	public function getRepayUrl($order)
	{
	    $orderId = $order->getRealOrderId();

	    $url = Mage::getUrl('weixinmobile/processing/redirect', array('_secure' => true));
	    $url .= '?orderId=' . $orderId . '&repay=1';

	    return $url;
	}
	public function getRepayBaseUrl()
	{
	    $url = Mage::getUrl('weixinmobile/processing/redirect', array('_secure' => true));
	    return $url;
	}
}
