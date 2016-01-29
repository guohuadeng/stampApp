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
class FME_Productattachments_Model_Productcats extends Mage_Core_Model_Abstract {

    public function _construct() {

        parent::_construct();
        $this->_init('productattachments/productcats');
    }

    /*
     * Add Parent Category Check
     * @return Array
     */

    public function addParnetCategory($left_node, $new_node) {
        return $this->getResource()->addNode($left_node, $new_node);
    }

    /*
     * Add Child Category Check
     * @return Array
     */

    public function addChildCategory($left_node) {
        return $this->getResource()->addChildNode($left_node, $left_node + 1);
    }

    /*
     * Delete Category
     * @return Array
     */

    public function deleteCategory($nodeId) {
        return $this->getResource()->deleteNodeRecursive($nodeId);
    }

    /*
     * Update Status Of Category
     * @return Array
     */

    public function changeStatus($node_id, $status) {
        return $this->getResource()->setNodeStatusRecursive($node_id, $status);
    }

    /*
     * Update Status Of Category
     * @return Array
     */

    public function getChilderns($node_name) {
        return $this->getResource()->getLocalSubNodes($node_name);
    }

    /*
     * Ger Parent ID
     * @return Array
     */

    public function getParentID($node_id) {
        return $this->getResource()->getParentNodeID($node_id);
    }

    public function getGridData() {
        return $this->getResource()->getGrid();
    }

    /*
     * Checks whether there is a category with the same URL key among the stores the category belongs to
     * @return bool
     */

    public function isUrlKeyUsed() {
        $storeIds = $this->getCategoryStoreIds();
        if (!is_array($storeIds))
            $storeIds = explode(',', $storeIds);

        $sameUrlCategoryStoreIds = $this->getResource()->getSameUrlCategoryStoreIds($this->getId(), $this->getCategoryUrlKey());

        $res = array_intersect($storeIds, $sameUrlCategoryStoreIds);
        return !empty($res);
    }

    protected function _afterLoad() {
        if (is_null($storeIds = $this->getCategoryStoreIds()))
            $this->setCategoryStoreIds($this->getResource()->getStoreIds($this->getId()));
        elseif (!is_array($storeIds))
            $this->setCategoryStoreIds(array_unique(explode(',', $storeIds)));

        return parent::_afterLoad();
    }

    public function afterLoad() {
        $this->_afterLoad();
    }

    /*
     * Loads itself using the URL key parameter
     * @param string $urlKey URL key used to identify the category
     */

    public function loadByUrlKey($urlKey) {
        $id = $this->getResource()->getIdByUrlKey($urlKey);
        return $this->load($id);
    }

}
