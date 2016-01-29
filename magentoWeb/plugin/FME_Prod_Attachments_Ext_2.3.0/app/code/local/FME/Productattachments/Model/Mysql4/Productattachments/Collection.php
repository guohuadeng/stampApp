<?php

/**
 * Productattachments extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Productattachments
 * @author     RT <rafay.tahir@unitedsol.net>
 * @copyright  Copyright 2015 � fmeextensions.com All right reserved
 */
class FME_Productattachments_Model_Mysql4_Productattachments_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('productattachments/productattachments');
    }

    public function addAttachmentIdFilter($id = 0) {
        $this->getSelect()
                ->where('related.productattachments_id=?', (int) $id);

        return $this;
    }

    public function addStoreFilter($store) {

        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        $this->getSelect()->join(
                        array('store_table' => $this->getTable('productattachments_store')), 'main_table.productattachments_id = store_table.productattachments_id', array()
                )
                ->where('store_table.store_id in (?)', array(0, $store));

        return $this;
    }

    public function addEnableFilter($status) {
        $this->getSelect()
                ->where('main_table.status = ?', $status);
        return $this;
    }

    public function addCategoryFilter($id) {
        $this->getSelect()
                ->join(array('cat' => $this->getTable('productattachments/productcats')), 'main_table.cat_id=cat.category_id')
                ->where('cat.category_name LIKE ?', "%$id%");
        return $this;
    }

}
