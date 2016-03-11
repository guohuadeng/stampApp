<?php
/**
 * @author Amasty Team
 * @copyright Amasty
 * @package Amasty_Fpc
 */

class Amasty_Fpc_Backend_Database extends Varien_Cache_Backend_Database
{
    /**
     * Overrides buggy Magento implementation
     * @param string $id
     * @param int    $extraLifetime
     *
     * @return bool|int
     * @throws Zend_Db_Adapter_Exception
     */
    public function touch($id, $extraLifetime)
    {
        if ($this->_options['store_data']) {
            return $this->_getAdapter()->update(
                $this->_getDataTable(),
                array('expire_time'=>new Zend_Db_Expr('expire_time+'.$extraLifetime)),
                array('id=?'=>$id, 'expire_time = 0 OR expire_time>?'=>time())
            );
        } else {
            return true;
        }
    }
}
