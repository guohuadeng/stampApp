<?php

class Magegiant_Magegiantcore_Model_Mysql4_Feedbackmessage extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('magegiantcore/feedbackmessage', 'feedbackmessage_id');
    }
}