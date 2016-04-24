<?php

class Alipaymate_WeixinMobile_ProcessingController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getHelper()
    {
        return Mage::helper('weixinmobile');
    }

    /**
     * when customer selects Weixin payment method
     */
    public function redirectAction()
    {
        try {
            $orderId = $this->_getOrderId();
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

            if (!$order->getId()) {
                Mage::throwException(Mage::helper('weixinmobile')->__('No order for processing'));
            }

            $this->getResponse()->setBody($this->getLayout()->createBlock('weixinmobile/redirect')->toHtml());

            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckout()->addError($e->getMessage());
        } catch(Exception $e) {
            Mage::logException($e);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Weixin Return
     */
    public function returnAction()
    {
        try {
            $orderId = $this->_getOrderId();
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($orderId);
            $order_id = $order->getId();

            $payment = Mage::getModel('weixinmobile/payment');
            $config  = $payment->prepareConfig();

            $weixin = Mage::getModel('weixinmobile/core');
            $weixin->setConfig($config);

            // check order is paid?
            if ($weixin->paid('', $orderId)) {
                //header('Location: ' . Mage::getUrl('checkout/onepage/success', array('_secure' => true)));
                header('Location: ' . Mage::getUrl('sales/order/view', array(
                  '_secure' => true,
                  'order_id' => $order_id,
                  'status' => true,
                  'message' => Mage::helper('checkout')->__('Thank you for your purchase!')
                )));
                exit();
            }
        } catch(Exception $e) {
            $session->addError($e->getMessage());
        }

        header('Location: ' . Mage::getUrl('checkout/onepage/failure', array('_secure' => true)));
        exit();
    }


    /**
     * Weixin notify
     */
    public function notifyAction()
    {
        $helper = $this->_getHelper()->setNotifyLog();

        $notifyXml = file_get_contents("php://input");

        if (empty($notifyXml)) {
            $notifyXml = $GLOBALS['HTTP_RAW_POST_DATA'];
        }


        $helper->log('weixinmobile-notify', $notifyXml);

        $payment = Mage::getModel('weixinmobile/payment');
        $config  = $payment->prepareConfig();

        $weixin = Mage::getModel('weixinmobile/core');
        $weixin->setConfig($config);

        $notify = $weixin->convertXmlToArray($notifyXml);

        // check sign
        if (!$weixin->verifyNotification($notify)) {
            $this->error();
        }

        // check order payment status
        if (!$weixin->paid($notify['transaction_id'])) {
            $this->error();
        }

        // update order status
        if ($notify['return_code']=='SUCCESS' && $notify['result_code']=='SUCCESS') {
            try {
                $orderId = $notify['out_trade_no'];

                $order = Mage::getModel('sales/order');
                $order->loadByIncrementId($orderId);
                if ($order->canInvoice()) {
                    $status = Mage::getStoreConfig('payment/weixin/order_status_payment_accepted');
                    $paymentcode = $order->getPayment()->getMethodInstance()->getCode();
                    $message = '';
                    //change payment，and log，这是因为要适用于所有类型的微信支付
                    $payment = $order->getPayment();
                    if ($paymentcode!='weixinmobile') {
                      $message = 'Payment change:'.$paymentcode.'>>weixinmobile. ';
                      $payment->setMethod('weixinmobile'); // Assuming 'test' is updated payment method
                      $payment->save();
                      $helper->log('order payment', $message);
                      }

                    $helper->log('order status', $status);

                    if (! $status) {
                        $status = Mage_Sales_Model_Order::STATE_PROCESSING;
                    }
                    $message = $message.Mage::helper('weixinmobile')->__('Payment successful') ;

                    $order->addStatusToHistory($status, $message);
                    $order->sendNewOrderEmail();
                    $order->setEmailSent(true);
                    $order->setIsCustomerNotified(true);
                    $order->save();

                    // make invoice
                    $invoice = $order->prepareInvoice();
                    $invoice->register()->capture();
                    Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();
                }

                $this->success();
            } catch (Exception $e) {
                $helper->log('weixinmobile-notify', $e->getMessage());
                $this->error();
            }
        }

        $this->error();
    }

    private function success()
    {
        $xml = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        echo $xml;
        exit();
    }


    private function error()
    {
        $xml = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>';
        echo $xml;
        exit();
    }

    /**
     * Get orderID model
     *
     * @return orderId
     */
    private function _getOrderId()
    {
        $request = $this->getRequest()->getParams();

        if (isset($request['orderId']) && $request['orderId'] > '') {
            $orderId = $request['orderId'];
        } else {
            $session = $this->_getCheckout();
            $orderId = $session->getLastRealOrderId();
        }
		  return $orderId;
    }

}