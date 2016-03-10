<?php

class Magegiant_Magegiantcore_Model_Mysql4_Logger extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('magegiantcore/logger', 'log_id');
    }
}