<?php
class Sunpop_StampCustomer_Model_Mysql4_Stampcustomer extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("stampcustomer/stampcustomer", "a_id");
    }
}