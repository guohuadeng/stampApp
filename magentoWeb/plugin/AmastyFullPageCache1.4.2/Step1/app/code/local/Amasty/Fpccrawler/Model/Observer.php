<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */


/**
 * @author Amasty
 */
define("MAX_ROWS_BEFORE_CLEAN", "500");
define("FILE_LOCK_GENERATE", "amfpccrawler_lock_generate.lock");
define("FILE_LOCK_PROCESS", "amfpccrawler_lock_process.lock");

class Amasty_Fpccrawler_Model_Observer
{

    public function generateQueue()
    {
        // check if crawler enabled
        if (!Mage::getStoreConfig('amfpccrawler/general/enabled')) {
            die('FpcCrawler is not enabled');
        }

        // check if another cron is still running
        $lockFile = Mage::getBaseDir('tmp') . DS . FILE_LOCK_GENERATE;
        $fp       = fopen($lockFile, 'w');
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            die('Another FpcCrawler cron job already working');// another lock detected
        }

        /**@var Amasty_Fpccrawler_Helper_Data $helper */
        /** @var Amasty_Fpccrawler_Model_Resource_Queue $res */
        $cnt    = 0;
        $helper = Mage::helper('amfpccrawler');
        $source = $helper->getQueueSource();
        $resource = Mage::getResourceModel('amfpccrawler/queue');

        foreach ($source as $item) {
            // add only real URLs
            if (strlen($item['url']) > 5) {
                $bind = array($item['url'], $item['rate'], $item['rate']);

                $res = $resource->addToQueue($bind);
                if (!$res) {
                    $helper->logDebugMessage('queue_add', 'Failed to add queue: ' . $item['url']);
                }
            } else {
                $helper->logDebugMessage('queue_add', 'Url is too short: ' . $item['url']);
            }
            // empty queue every X rows inserted
            if ($cnt++ > MAX_ROWS_BEFORE_CLEAN) {
                $cnt = 0;
                $resource->cleanQueue();
            }
        }

        $resource->cleanQueue();

        // remove the lock
        fclose($fp);

        return true;
    }

    public function processQueue()
    {
        // check if crawler enabled
        if (!Mage::getStoreConfig('amfpccrawler/general/enabled')) {
            die('FpcCrawler is not enabled');
        }

        // check if another cron is still running
        $lockFile = Mage::getBaseDir('tmp') . DS . FILE_LOCK_PROCESS;
        $fp       = fopen($lockFile, 'w');
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            die('Another FpcCrawler cron job already working');// another lock detected
        }

        /**@var Amasty_Fpccrawler_Helper_Data $helper */
        /**@var Amasty_Fpccrawler_Model_Resource_Queue_Collection $source */
        /**@var Amasty_Fpccrawler_Model_Resource_Log $log */
        $logNum    = 0;
        $sourceNum = 0;
        $linksDone = 0;
        $helper    = Mage::helper('amfpccrawler');
        $log       = Mage::getResourceModel('amfpccrawler/log');
        $limit     = Mage::getStoreConfig('amfpccrawler/queue/process_limit');
        $source    = Mage::getResourceModel('amfpccrawler/queue_collection')->setOrder('rate', 'DESC');
        $source    = array_values($source->getItems());//remove associative array keys
        $log->cleanLog();

        // get config data
        $stores         = Mage::getStoreConfig('amfpccrawler/general/store');
        $mobiles   = Mage::getStoreConfig('amfpccrawler/general/mobile') ? array(true) : array();
        $currencies     = Mage::getStoreConfig('amfpccrawler/general/currency');
        $customerGroups = Mage::getStoreConfig('amfpccrawler/general/customer_group');

        // reverse string-stored values into arrays
        $stores         = explode(',', trim($stores, ','));
        $currencies     = explode(',', trim($currencies, ','));
        $customerGroups = explode(',', trim($customerGroups, ','));

        // filter parameters
        if (is_array($customerGroups) && isset($customerGroups[0]) && $customerGroups[0] == '0') {
            unset($customerGroups[0]);
        }

        // add false items into arrays
        array_unshift($stores, false);
        array_unshift($mobiles, false);
        array_unshift($currencies, false);
        array_unshift($customerGroups, false);

        // loop through each link and receive it
        //     $limit --> number of real requests that can be done for one
        //     cron job running without counting any already cached pages
        while ($linksDone <= $limit && isset($source[$sourceNum])) {
            $link = $source[$sourceNum++];
            // loop through each store for all CUSTOMER GROUPS
            foreach ($customerGroups as $customerGroup) {
                // loop through each STORE
                foreach ($stores as $store) {
                    // get MOBILE or normal versions of a page
                    foreach ($currencies as $currency) {
                        // get MOBILE or normal versions of a page
                        foreach ($mobiles as $mobile) {
                            // check if we need to update this page cache
                            /**@var Varien_Object $data */
                            $data = new Varien_Object();
                            $data->setData('customerGroup', $customerGroup);
                            $data->setData('url', ($link->getUrl()));
                            $data->setData('currency', $currency);
                            $data->setData('mobile', $mobile);
                            $data->setData('store', $store);
                            $data->setData('hasCache', false);
                            Mage::dispatchEvent('amfpccrawler_process_link', array('data' => $data));

                            // proceed update if needed
                            if ($data->getData('hasCache') == false) {
                                // get URL
                                list($res, $status) = $helper->getUrl($link->getUrl(), $customerGroup, $store, $currency, $mobile, $link->getRate(), true);

                                // check result
                                if (!$status) {
                                    $helper->logDebugMessage('queue_process', 'Failed to request: ' . $link->getUrl());
                                }

                                // clear log every X rows inserted
                                $linksDone++;
                                if ($logNum++ > MAX_ROWS_BEFORE_CLEAN) {
                                    $logNum = 0;
                                    $log->cleanLog();
                                }
                            } else {
                                /**@var Amasty_Fpccrawler_Model_Resource_Log $log */
                                $log = Mage::getResourceModel('amfpccrawler/log');
                                $log->addToLog($link->getUrl(), $customerGroup, $store, $currency, $mobile, $link->getRate(), 0, 0);
                            }
                        }
                    }
                }
            }

            // finally delete the link after walk-through
            $link->delete();
        }

        // remove the lock
        fclose($fp);

        return true;
    }

    public function checkCURL(Varien_Event_Observer $observer)
    {
        $params = Mage::app()->getRequest()->getParams();
        if (isset($params['section']) && $params['section'] == 'amfpccrawler') {
            // check if CURL lib exists
            if (!function_exists('curl_version')) {
                Mage::getSingleton('adminhtml/session')->addError('FPC Crawler will not work because PHP library CURL is disabled or not installed');
            }

            // echo the notice with Approx. Queue Processing Time
            $time = Mage::getResourceModel('amfpccrawler/log')->getQueueProcessingTime();
            $msg  = Mage::getModel('core/layout')
                        ->createBlock('core/template')
                        ->setProcessing($time)
                        ->setForAdminNotice(true)
                        ->setTemplate('amasty/amfpccrawler/charts/queueProcessing.phtml')
                        ->toHtml();
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);

            if (Mage::getStoreConfig('amfpccrawler/advanced/show_notifications')) {
                // check max_execution_time and warn the user
                $maxLifetime = ini_get('max_execution_time');
                $maxLifetime = $maxLifetime >= 0 ? $maxLifetime : 30;
                $processingTime = Mage::getResourceModel('amfpccrawler/log')->getQueueProcessingTime();
                if ($processingTime['cronProcessingTime'] > $maxLifetime && $maxLifetime != 0) {
                    $msg = Mage::helper('amfpccrawler')->__('Your one cron job processing time(' . $processingTime['cronProcessingTime'] . 's) is more than PHP allows(' . $maxLifetime . 's). Please, adjust your crawler settings to lower one cron job executing time!');
                    Mage::getSingleton('adminhtml/session')->addWarning($msg);
                }
                if ($processingTime['cronProcessingTime'] > 30) {
                    $msg = Mage::helper('amfpccrawler')->__('Your one cron job processing time(' . $processingTime['cronProcessingTime'] . 's) is more than PHP settings allows by default (30s). Please, check your max_execution_time PHP settings or adjust your crawler settings to lower one cron job processing time!');
                    Mage::getSingleton('adminhtml/session')->addNotice($msg);
                }
            }
        }
    }

}