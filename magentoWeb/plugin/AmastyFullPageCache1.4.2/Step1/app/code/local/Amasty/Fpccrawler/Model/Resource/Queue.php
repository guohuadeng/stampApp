<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */
class Amasty_Fpccrawler_Model_Resource_Queue extends Mage_Core_Model_Resource_Db_Abstract
{
    public function addToQueue($bind)
    {
        $res     = Mage::getSingleton('core/resource');
        $query   = "" . 'INSERT INTO `' . $res->getTableName('amfpccrawler/queue') . "` (`url`, `rate`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `rate` = ?;";
        $data    = $res->getConnection('core_write');
        $results = $data->query($query, $bind);

        return $results;
    }

    public function cleanQueue()
    {
        $limit = Mage::getStoreConfig('amfpccrawler/queue/queue_limit');
        $res   = Mage::getSingleton('core/resource');
        $data  = $res->getConnection('core_write');
        // get number of rows total
        $query    = 'SELECT COUNT(*) AS count FROM ' . $res->getTableName('amfpccrawler/queue');
        $count    = $data->fetchOne($query);
        $deleting = $count - $limit;

        // empty last rows
        if ($deleting > 0) {
            $query = "" . 'DELETE FROM `' . $res->getTableName('amfpccrawler/queue') . '` WHERE `queue_id`>0 ORDER BY `rate` LIMIT ' . (int)$deleting;
            $data->query($query);
        }
    }

    public function flushQueue()
    {
        $this->_getWriteAdapter()->query('TRUNCATE TABLE ' . $this->getMainTable());

        return $this;
    }

    protected function _construct()
    {
        $this->_init('amfpccrawler/queue', 'queue_id');
    }

}
