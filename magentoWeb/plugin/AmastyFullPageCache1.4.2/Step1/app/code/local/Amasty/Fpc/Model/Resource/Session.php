<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */

class Amasty_Fpc_Model_Resource_Session extends Mage_Core_Model_Mysql4_Session
{
    const SEESION_MAX_COOKIE_LIFETIME = 3155692600;

    public function getLifeTime()
    {
        $stores = Mage::app()->getStores();
        if (empty($stores))
            return self::SEESION_MAX_COOKIE_LIFETIME;
        else
            return parent::getLifeTime();
    }
}
