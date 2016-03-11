<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */

class Amasty_Fpc_Model_Resource_Redis_Session extends Cm_RedisSession_Model_Session
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
