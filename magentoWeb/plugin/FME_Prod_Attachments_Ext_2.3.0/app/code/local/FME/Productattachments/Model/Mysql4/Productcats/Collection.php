<?php

/**
 * Product Attachments extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Product Attachments
 * @copyright  Copyright 2010 � free-magentoextensions.com All right reserved
 * */
class FME_Productattachments_Model_Mysql4_Productcats_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('productattachments/productcats');
    }

    /**
     * deprecated after 1.4.0.1, use toOptionIdArray()
     *
     * @return array
     */
    public function toOptionArray() {
        return $this->_toOptionArray('category_id', 'category_name');
    }

    /*
     * Covers original bug in Varien_Data_Collection_Db
     */

    public function getSelectCountSql() {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();

        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);

        $countSelect->from('', 'COUNT(DISTINCT main_table.category_id)');

        return $countSelect;
    }

    /*
     * Covers original bug in Mage_Core_Model_Mysql4_Collection_Abstract
     */

    public function getAllIds() {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->reset(Zend_Db_Select::HAVING);
        $idsSelect->from(null, 'main_table.' . $this->getResource()->getIdFieldName());
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * Add filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @param bool $withAdmin
     * @return Mage_Cms_Model_Resource_Block_Collection
     */
    /* public function addStoreFilter($store, $withAdmin = true)
      {
      if ($store instanceof Mage_Core_Model_Store) {
      $store = array($store->getId());
      }

      if (!is_array($store)) {
      $store = array($store);
      }

      if ($withAdmin) {
      $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
      }

      $this->addFilter('store', array('in' => $store), 'public');

      return $this;
      } */




    public function addStoreFilter($store) {

        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        $this->getSelect()->join(
                        array('store_table' => $this->getTable('productattachments_category_store')), 'main_table.category_id = store_table.category_id', array()
                )
                ->where('store_table.store_id in (?)', array(0, $store));

        return $this;
    }

    public function addStatusFilter($enabled = 1) {
        $this->getSelect()
                ->where('main_table.category_status=?', (int) $enabled);

        return $this;
    }

    public function addRootCategoryFilter($id = 0) {
        $this->getSelect()
                ->where('main_table.parent_category_id != ?', (int) $id);

        return $this;
    }

    public function addPortfolioFilter($id = 0) {
        $this->getSelect()
                ->where('link.news_id=?', (int) $id);

        return $this;
    }

    public function addCategoryFilter($cat) {

        $this->getSelect()
                ->where('category_id= ?', $cat);

        return $this;
    }

}
