<?php

class Sunpop_WeixinApp_ProcessingController extends Mage_Core_Controller_Front_Action
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
        return Mage::helper('weixinapp');
    }

    /**
     * when customer selects Weixin payment method
     */
    public function redirectAction()
    {
        try {
            $request = $this->getRequest()->getParams();

            if (isset($request['orderId']) && $request['orderId'] > '') {
                $orderId = $request['orderId'];
            } else {
                $session = $this->_getCheckout();
                $orderId = $session->getLastRealOrderId();
            }

            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

            if (!$order->getId()) {
                Mage::throwException(Mage::helper('weixinapp')->__('No order for processing'));
            }

            $this->getResponse()->setBody($this->getLayout()->createBlock('weixinapp/redirect')->toHtml());

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
        $session = $this->_getCheckout();

        try {
            $orderId = $session->getLastRealOrderId();

            $payment = Mage::getModel('weixinapp/payment');
            $config  = $payment->prepareConfig();

            $weixin = Mage::getModel('weixinapp/core');
            $weixin->setConfig($config);

            // check order is paid?
            if ($weixin->paid('', $orderId)) {
                header('Location: ' . Mage::getUrl('checkout/onepage/success', array('_secure' => true)));
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


        $helper->log('weixinapp-notify', $notifyXml);

        $payment = Mage::getModel('weixinapp/payment');
        $config  = $payment->prepareConfig();

        $weixin = Mage::getModel('weixinapp/core');
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
                    if ($paymentcode!='weixinapp') {
                      $message = 'Payment change:'.$paymentcode.'>>weixinapp. ';
                      $payment->setMethod('weixinapp'); // Assuming 'test' is updated payment method
                      $payment->save();
                      $helper->log('order payment', $message);
                      }
                    // make invoice
                    $invoice = $order->prepareInvoice();
                    $invoice->register()->capture();
                    Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();

                    if ($order->canShip())  { //重新支付的话，如果已发货，则不需要再更改订单状态，开invoice已OK
                      if (! $status) {
                          $status = Mage_Sales_Model_Order::STATE_PROCESSING;
                      }
                      $helper->log('order status', $status);

                      $message = $message.Mage::helper('weixinapp')->__('Payment successful') ;

                      $order->addStatusToHistory($status, $message);
                      $order->sendNewOrderEmail();
                      $order->setEmailSent(true);
                      $order->setIsCustomerNotified(true);
                      $order->save();
                    }
                }

                $this->success();
            } catch (Exception $e) {
                $helper->log('weixinapp-notify error', $e->getMessage());
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

}