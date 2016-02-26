<?php

/**
 * Rating collection resource model
 *
 * @category    Mage
 * @package     Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Alipaymate_Weixinlogin_Model_Resource_Identifiers_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('weixinlogin/identifiers');
    }    
}
