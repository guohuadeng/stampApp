<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */
class Amasty_Fpccrawler_Model_Resource_Log extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Cleans log - delete rows with low rate first
     */
    public function cleanLog()
    {
        $limit = Mage::getStoreConfig('amfpccrawler/log/limit');
        $res   = Mage::getSingleton('core/resource');
        $data  = $res->getConnection('core_write');
        // get number of rows total
        $query    = 'SELECT COUNT(*) AS count FROM ' . $res->getTableName('amfpccrawler/log');
        $count    = $data->fetchOne($query);
        $deleting = $count - $limit;

        // empty last rows
        if ($deleting > 0) {
            $query = "" . 'DELETE FROM `' . $res->getTableName('amfpccrawler/log') . '` WHERE `log_id`>0 ORDER BY `date` DESC LIMIT ' . (int)$deleting;
            $data->query($query);
        }
    }

    /**
     * Adding info to log
     *
     * @param $url
     * @param $group
     * @param $storeId
     * @param $currency
     * @param $mobile
     * @param $rate
     * @param $status
     * @param $load
     *
     * @return mixed
     */
    public function addToLog($url, $group, $storeId, $currency, $mobile, $rate, $status, $load)
    {
        $res     = Mage::getSingleton('core/resource');
        $query   = "" . 'INSERT INTO `' . $res->getTableName('amfpccrawler/log') . "`
                            (`url`, `customer_group`, `store`, `currency`, `mobile`, `rate`, `status`, `page_load`, `date`)
                            VALUES (?, ?, ? ,?, ?, ?, ?, ?, ?)";
        $data    = $res->getConnection('core_write');
        $bind    = array($url, $group, $storeId, $currency, $mobile, $rate, $status, $load, time());
        $results = $data->query($query, $bind);

        return $results;
    }

    /**
     * Gets statistics about status codes for the last X days
     *
     * @param int $period Time in days
     *
     * @return mixed
     */
    public function getStatusCodes($period = 30)
    {
        $period = $this->getPeriodInSeconds($period);

        $res     = Mage::getSingleton('core/resource');
        $query   = "" . 'SELECT COUNT(log_id) as count, status FROM `' . $res->getTableName('amfpccrawler/log') . "`
                         WHERE   date >= ?
                         GROUP BY status
                         LIMIT 50000 ";
        $data    = $res->getConnection('core_read');
        $bind    = array($period);
        $results = $data->fetchAll($query, $bind);

        foreach ($results as &$item) {
            $item['status'] = $item['status'] . ' (' . Mage::helper('amfpccrawler')->getStatusCodeDescription($item['status']) . ')';
        }

        return $results;
    }

    /**
     * Converts days in seconds
     *
     * @param $period int  Time in days
     *
     * @return int         Time in seconds
     */
    private function getPeriodInSeconds($period)
    {
        return time() - ($period * 60 * 60 * 24);
    }

    /**
     * Gets statistics about page load time for the last X days
     *
     * @param int $period Time in days
     *
     * @return mixed
     */
    public function getPageLoadTime($period = 30)
    {
        $period = $this->getPeriodInSeconds($period);

        $res     = Mage::getSingleton('core/resource');
        $query   = "" . 'SELECT max(page_load) as page_load,url,store,currency,customer_group,mobile,rate,date  FROM `' . $res->getTableName('amfpccrawler/log') . "`
                         WHERE   date >= ?
                         GROUP BY url
                         ORDER BY page_load DESC
                         LIMIT 20 ";
        $data    = $res->getConnection('core_read');
        $bind    = array($period);
        $results = $data->fetchAll($query, $bind);

        return $results;
    }

    /**
     * Gets statistics about crawled pages for the last X days
     *
     * @param int $period Time in days
     *
     * @return mixed
     */
    public function getCrawledPages($period = 30)
    {
        $period = $this->getPeriodInSeconds($period);

        $res     = Mage::getSingleton('core/resource');
        $query   = "" . 'SELECT COUNT(log_id) as count, ((date DIV 86400)*86400) as day  FROM `' . $res->getTableName('amfpccrawler/log') . "`
                         WHERE   date >= ?
                         GROUP BY day
                         ORDER BY day
                         LIMIT 5000 ";
        $data    = $res->getConnection('core_read');
        $bind    = array($period);
        $results = $data->fetchAll($query, $bind);

        return $results;
    }

    /**
     *  Returns information about queue processing time
     *
     */
    public function getQueueProcessingTime()
    {
        $result = array();
        $res    = Mage::getSingleton('core/resource');
        $data   = $res->getConnection('core_write');

        // get AVERAGE page loading time
        $query          = "" . 'SELECT AVG(page_load) AS page_load  FROM ' . $res->getTableName('amfpccrawler/log');
        $avgTimeFromLog = (float)$data->fetchOne($query);

        // if no matches in log - just take one URL for sample probe
        if ($avgTimeFromLog <= 0) {
            Mage::helper('amfpccrawler')->getUrl(Mage::getBaseUrl('web'), false, false, false, false, 0, true);
            $query          = "" . 'SELECT AVG(page_load) AS page_load  FROM ' . $res->getTableName('amfpccrawler/log');
            $avgTimeFromLog = (float)$data->fetchOne($query);
        }
        // if average time is less than a second
        $avgTimeFromLog = $avgTimeFromLog < 1 ? 1 : $avgTimeFromLog;

        // get total rows count in current queue
        $query          = "" . 'SELECT COUNT(queue_id) AS count  FROM ' . $res->getTableName('amfpccrawler/queue');
        $queueRowsCount = (float)$data->fetchOne($query);

        // get options count selected for queue processing
        $urlOptionsCount = 1;
        $options         = array(
            Mage::getStoreConfig('amfpccrawler/general/customer_group'),
            Mage::getStoreConfig('amfpccrawler/general/store'),
            Mage::getStoreConfig('amfpccrawler/general/currency'),
            Mage::getStoreConfig('amfpccrawler/general/mobile'),
        );
        foreach ($options as $key => $item) {
            $cnt             = count(explode(',', $item));
            $urlOptionsCount = $urlOptionsCount * ($cnt + 1);
        }
        if ($options[3] == '0') $urlOptionsCount /= 2; // mobile view has Yes/No options, so decrease the vale of counter
        if (!empty($options[0]) && $options[0][0] == '0') $urlOptionsCount /= 2; // we do not process NOT LOGGED IN customer group

        $queueProcessCount            = Mage::getStoreConfig('amfpccrawler/queue/process_limit');
        $queueProcessCount            = $queueProcessCount > $queueRowsCount ? $queueRowsCount : $queueProcessCount;

        // prepare result array
        $result['queueProcessingTime'] = (int)$avgTimeFromLog * $queueRowsCount * $urlOptionsCount * 1.10;
        $result['cronProcessingTime'] = (int)$avgTimeFromLog * $queueProcessCount * $urlOptionsCount * 1.10;
        $result['avgProcessingTime']   = (float)$avgTimeFromLog;
        $result['urlOptionsCount']     = (int)$urlOptionsCount;
        $result['queueRowsCount']      = (int)$queueRowsCount;

        return $result;
    }

    /**
     * Just construct method
     */
    protected function _construct()
    {
        $this->_init('amfpccrawler/log', 'log_id');
    }
}
