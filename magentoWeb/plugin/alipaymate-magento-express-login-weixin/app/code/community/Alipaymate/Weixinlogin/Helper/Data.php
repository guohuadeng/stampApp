<?php

class Alipaymate_Weixinlogin_Helper_Data extends Mage_Payment_Helper_Data
{
    private $_logDir  = 'weixinlogin';
    private $_logFile = null;
    private $_logType = null;
    private $_debug   = false;

    public function __construct()
    {
        $this->_debug = true;
    }

    public function log($title, $message)
    {
        if (!$this->_debug) {
            return true;
        }

        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }

        $line = "== {$title} ==:\r\n{$message}\r\n";

        $logFile = $this->getLogFile();

        if ($logFile) {
            return error_log($line, 3, $logFile);
        }

        return false;
    }

    public function setReturnLog()
    {
        $this->_logType = '_return';
        $this->_logFile = null;

        return $this;
    }

    public function setNotifyLog()
    {
        $this->_logType = '_notify';
        $this->_logFile = null;

        return $this;
    }

    public function getLogFile()
    {
        if (!$this->_logFile) {
            $logDir = Mage::getBaseDir('log') . DS . $this->_logDir;

            if (!file_exists($logDir)) {
                if (!mkdir($logDir, 0777)) {
                    return false;
                }
            }

            $rand = substr(md5(rand()), 0, 6);

            if ($this->_logType) {
                $logFile = $logDir . DS . $this->_logType . '-' . date('Ymd'). '.' . $rand . '.log';
            } else {
                $logFile = $logDir . DS . date('YmdHis'). '.' . $rand . '.log';
            }

            $this->_logFile = $logFile;
        }

        return $this->_logFile;
    }

    public function getCustomersByEmail($email)
    {
        $customer = Mage::getModel('customer/customer');

        $collection = $customer->getCollection()
                ->addFieldToFilter('email', $email)
                ->setPageSize(1);

        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter(
                'website_id',
                Mage::app()->getWebsite()->getId()
            );
        }

        return $collection;
    }
    

    public function createCustomer($email, $firstName, $lastName, $alpayUserId, $token) {
        $customer = Mage::getModel('customer/customer');
        
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId())
                ->setEmail($email)
                ->setFirstname($firstName)
                ->setLastname($lastName)
                ->setWeixinToken($token)
                ->setPassword($customer->generatePassword(10))
                ->save();
        
        $customer->setConfirmation(null);
        $customer->save();
                                      
        return $customer;
    }    
}