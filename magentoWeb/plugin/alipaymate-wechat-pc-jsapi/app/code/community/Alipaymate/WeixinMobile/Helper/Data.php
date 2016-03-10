<?php

class Alipaymate_WeixinMobile_Helper_Data extends Mage_Payment_Helper_Data
{
    private $_logDir  = 'weixinmobile';
    private $_logFile = null;
    private $_logType = null;
    private $_debug   = false;

    public function __construct()
    {
        $this->_debug = Mage::getStoreConfig('payment/weixinmobile/debug');
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
        $logDir = Mage::getBaseDir('log') . DS . $this->_logDir;

        if (!$this->_logFile) {
            if (!file_exists($logDir)) {
                if (!mkdir($logDir, 0777)) {
                    return false;
                }

                $this->createHtaccessFile($logDir);
                $this->createIndexFile($logDir);
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


    /**
     * Create log directory for 'weixinmobile' payment method
     * Location is /public_html/var/log/weixinmobile
     */
    private function createLogDir()
    {
        $logDir = Mage::getBaseDir('log') . DS . $this->_logDir;

        if (!file_exists($logDir)) {
            if (mkdir($farLogRoot, 0777, true)) {
                $this->createHtaccessFile($farLogRoot);
            }
        }
    }

    /**
     * Create .htaccess file and index.html to forbid visit log file
     *
     * @return true or false
     */
    private function createHtaccessFile($path)
    {
        $htFile = $path . DS . '.htaccess';

        if (!file_exists($htFile)) {
            return file_put_contents($htFile, "Order deny,allow\r\nDeny from all");
        }

        return true;
    }

    /**
     * Create index.html to forbid visit log file
     *
     * @return true or false
     */
    private function createIndexFile($path)
    {
        $indexFile = $path . DS . 'index.html';

        if (!file_exists($indexFile)) {
            return file_put_contents($indexFile, "");
        }

        return true;
    }
}