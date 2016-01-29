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
class FME_Productattachments_Model_Mysql4_Productcats extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {

        $this->_init('productattachments/productcats', 'category_id');
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field = null) {
        if (strcmp($value, (int) $value) !== 0) {
            $field = 'category_url_key';
        }
        return parent::load($object, $value, $field);
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object) { 
        //Get Stores
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('productattachments_category_store'))
                ->where('category_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {

            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }

            $object->setData('store_id', $storesArray);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        $condition = $this->_getWriteAdapter()->quoteInto('category_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('productattachments_category_store'), $condition);



        foreach ((array) $object->getData('category_store_ids') as $store) {
            $storeArray = array();
            $storeArray['category_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('productattachments_category_store'), $storeArray);
        }


        return parent::_afterSave($object);
    }

    /*
     * Returns category_id => category_name associated array
     * @param int|empty $storeId The ID of the current store
     * @return array category_id => category_name
     */

    public static function getCategories() {
        $collection = Mage::getModel('productattachments/productcats')->getCollection();

        $collection->printlogquery(true);

        $res = array();

        foreach ($collection as $data)
            $res[$data['category_id']] = $data['cat'];
        return $res;
    }

    public static function toOptionArray() {
        $res = array();

        foreach (self::getCategories() as $key => $value)
            $res[] = array('value' => $key,
                'label' => $value);

        return $res;
    }

    /*
     * Returns IDs of the stores the category with the ID given belongs to
     * @param int $categoryId The ID of the category
     * @result array ID of the stores
     */

    public function getStoreIds($categoryId) {
        if (!$categoryId)
            return array();

        $db = $this->_getReadAdapter();

        $select = $db->select()
                ->from($this->getTable('productattachments/productattachments_category_store'), 'store_id')
                ->where('category_id=?', $categoryId);

        return $db->fetchCol($select);
    }

    /*
     * Returns the IDs of the stores that have categories with the same URL key as passed
     * @param int $categoryId The ID of current category, needed to exclude from result set
     * @param string $url URL key of the category
     * @return array The IDs of the stores
     */

    public function getSameUrlCategoryStoreIds($categoryId, $url) {
        if (!$url)
            return array();

        $db = $this->_getReadAdapter();

        $select = $db->select()
                ->from(array('c' => $this->getMainTable()), ''
                )
                ->joinInner(array('cs' => $this->getTable('productattachments/productattachments_category_store')), 'c.category_id=cs.category_id', array('store_ids' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT cs.store_id)'))
                )
                ->where('c.category_url_key=?', $url)
                ->group('c.category_id');

        if ($categoryId)
            $select
                    ->where('c.category_id<>?', $categoryId);

        if ($res = $db->fetchOne($select))
            return array_unique(explode(',', $res));
        else
            return array();
    }

    /*
     * Returns the ID of the category with the same URL key as passed
     * @param string $urlKey URL key
     * @result int The ID of the category
     */

    public function getIdByUrlKey($urlKey) {
        $db = $this->_getReadAdapter();

        $select = $db->select()
                ->from(array('c' => $this->getMainTable()), 'category_id'
                )
                ->joinLeft(array('cs' => $this->getTable('productattachments/productattachments_category_store')), 'c.category_id=cs.category_id', ''
                )
                ->where('c.category_url_key=?', $urlKey)
                ->where('cs.store_id=?', Mage::app()->getStore()->getId())
                ->limit(1);

        return $db->fetchOne($select);
    }

    /*     * *
     *
     * @fetch the full tree
     *
     * @param string $parent
     *
     * @return array
     *
     */

    public function fullTree($parent) {

        $db = $this->_getWriteAdapter();
        $stmt = $db->prepare("SELECT node.category_name FROM " . $this->getTable('productattachments/productatcats') . " AS node, news_category AS parent
								WHERE node.left_node BETWEEN parent.left_node AND parent.right_node
								AND parent.name = :parent ORDER BY node.left_node");
        $result->bindParam('parent', $parent);
        $result->execute();
        return $result->fetchALL(PDO::FETCH_ASSOC);
    }

    /*     * *
     *
     * @fetch the full tree
     *
     * @param string $parent
     *
     * @return array
     *
     */

    public function getGrid() {

        $db = $this->_getWriteAdapter();
        $stmt = $db->prepare("	SELECT CONCAT( REPEAT( '  ', (COUNT(parent.category_name) - 1) ), node.category_name) AS category_name, node.category_id
								,node.category_status,node.category_url_key,node.category_order,node.left_node,node.right_node
								FROM " . $this->getTable('productattachments/productatcats') . " AS node,
								" . $this->getTable('productattachments/productatcats') . " AS parent
								WHERE node.left_node BETWEEN parent.left_node AND parent.right_node
								GROUP BY node.category_name
								ORDER BY node.left_node");
        $stmt->execute();
        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     *
     * Find all leaf nodes
     *
     * @access public
     *
     * @return array
     *
     */
    public function leafNodes() {
        $db = $this->_getWriteAdapter();
        $stmt = $db->prepare("SELECT category_name FROM " . $this->getTable('productattachments/productatcats') . " WHERE right_node = left_node + 1");
        $stmt->execute();
        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     *
     * Retrieve a single path
     *
     * @access public
     *
     * @param $node_name
     *
     * @return array
     *
     */
    public function singlePath($node_id) {
        $db = $this->_getWriteAdapter();
        $stmt = $db->prepare("SELECT parent.category_name FROM " . $this->getTable('productattachments/productatcats') . " AS node, " . $this->getTable('productattachments/productattachments_cats') . " AS parent WHERE node.left_node BETWEEN parent.left_node AND parent.right_node AND node.category_id = '{$node_id}' ORDER BY node.left_node");
        $stmt->execute();
        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     *
     * Retrieve a depth of nodes
     *
     * @access public
     *
     * @param $node_name
     *
     * @return array
     *
     */
    public function getNodeDepth() {
        $db = $this->_getWriteAdapter();
        $stmt = $db->prepare("SELECT node.category_name, (COUNT(parent.name) - 1) AS depth FROM " . $this->getTable('productattachments/productatcats') . " AS node, " . $this->getTable('productattachments/productattachments_cats') . " AS parent WHERE node.left_node BETWEEN parent.left_node AND parent.right_node GROUP BY node.category_name ORDER BY node.left_node");
        $stmt->execute();
        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     *
     * Retrieve a subTree depth
     *
     * @access public
     *
     * @param $node_name
     *
     * @return array
     *
     */
    public function subTreeDepth($node_id) {
        $db = $this->_getWriteAdapter();
        $stmt = $db->prepare("SELECT node.category_name, (COUNT(parent.category_name) - 1) AS depth FROM " . $this->getTable('productattachments/productatcats') . " AS node, " . $this->getTable('productattachments/productatcats') . " AS parent WHERE node.left_node BETWEEN parent.left_node AND parent.right_node AND node.category_id = :node_id GROUP BY node.category_name ORDER BY node.left_node");
        $stmt->bindParam(':node_id', $node_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     *
     * @fetch local sub nodes only
     *
     * @access public
     *
     * @param $node_name
     *
     * @return array
     *
     */
    public function getLocalSubNodes($node_name) {
        $db = $this->_getWriteAdapter();
        $stmt = $db->prepare("SELECT node.category_id,node.category_name, (COUNT(parent.category_name) - (sub_tree.depth + 1)) AS depth,node.*
								FROM " . $this->getTable('productattachments/productatcats') . " AS node,
									" . $this->getTable('productattachments/productatcats') . " AS parent,
									" . $this->getTable('productattachments/productatcats') . " AS sub_parent,
									(
										SELECT node.category_name, (COUNT(parent.category_name) - 1) AS depth
										FROM " . $this->getTable('productattachments/productatcats') . " AS node,
										" . $this->getTable('productattachments/productatcats') . " AS parent
										WHERE node.left_node BETWEEN parent.left_node AND parent.right_node
										AND node.category_name = :node_name
										GROUP BY node.category_name
										ORDER BY node.left_node
									)AS sub_tree
									WHERE node.left_node BETWEEN parent.left_node AND parent.right_node
									AND node.left_node BETWEEN sub_parent.left_node AND sub_parent.right_node
									AND sub_parent.category_name = sub_tree.category_name
								GROUP BY node.category_name
								HAVING depth = 1
								ORDER BY node.left_node;");
        $stmt->bindParam(':node_name', $node_name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     *
     * @list categories and product count
     *
     * @access public
     *
     * @return array
     *
     */
    public function productCount() {
        $db = $this->_getWriteAdapter();
        $stmt = $db->prepare("SELECT parent.category_name, COUNT(products.category_name) AS product_count FROM " . $this->getTable('productattachments/productattachments_cats') . " AS node ," . $this->getTable('productattachments/productattachments_cats') . " AS parent, products  WHERE node.left_node BETWEEN parent.left_node AND parent.right_node AND node.category_id = products.category_id GROUP BY parent.category_name ORDER BY node.left_node");
        $stmt->execute();
        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     *
     * @list categories and product count
     *
     * @access public
     *
     * @return array
     *
     */
    public function getParentNodeID($node_id) {
        $db = $this->_getWriteAdapter();
        $stmt = $db->prepare("SELECT parent_id FROM " . $this->getTable('productattachments/productatcats') . " WHERE category_id = $node_id");
        $stmt->execute();
        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     *
     * @add a node
     *
     * @access public
     *
     * @param string $left_node
     * 
     * @param string $new_node
     *
     */
    public function addNode($node_id, $new_node) {

        $db = $this->_getWriteAdapter();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("SELECT @myRight := right_node FROM " . $this->getTable('productattachments/productatcats') . " WHERE category_id = :node_id");
            $stmt->bindParam(':node_id', $node_id);
            $stmt->execute();

            /*             * * increment the nodes by two ** */
            $db->exec("UPDATE " . $this->getTable('productattachments/productatcats') . " SET right_node = right_node + 2 WHERE right_node > @myRight");
            $db->exec("UPDATE " . $this->getTable('productattachments/productatcats') . " SET left_node = left_node + 2 WHERE left_node > @myRight");

            $stmt = $db->prepare("SELECT @myRight + 1 as lft, @myRight + 2 as rgt");
            $stmt->execute();
            /*             * * commit the transaction ** */
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception($e);
        }
        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     *
     * @Add child node
     * @ adds a child to a node that has no children
     *
     * @access public
     *
     * @param string $node_name The node to add to
     *
     * @param string $new_node The name of the new child node
     *
     * @return array
     *
     */
    public function addChildNode($node_id, $new_node) {
        $db = $this->_getWriteAdapter();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("SELECT @myLeft := left_node FROM " . $this->getTable('productattachments/productatcats') . " WHERE category_id=:node_id");
            $stmt->bindParam(':node_id', $node_id);
            $stmt->execute();

            $db->exec("UPDATE " . $this->getTable('productattachments/productatcats') . " SET right_node = right_node + 2 WHERE right_node > @myLeft");
            $db->exec("UPDATE " . $this->getTable('productattachments/productatcats') . " SET left_node = left_node + 2 WHERE left_node > @myLeft");

            $stmt = $db->prepare("SELECT @myLeft + 1 as lft, @myLeft + 2 as rgt");
            $stmt->execute();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception($e);
        }
        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     *
     * @Delete a leaf node
     *
     * @param string $node_id
     *
     * @access public
     *
     */
    public function deleteLeafNode($node_id) {
        $db = $this->_getWriteAdapter();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("SELECT @myLeft := left_node, @myRight := right_node, @myWidth := right_node - left_node + 1 FROM " . $this->getTable('productattachments/productattachments_cats') . " WHERE category_id = :node_id");
            $stmt->bindParam(':node_id', $node_id);
            $stmt->execute();
            $db->exec("DELETE FROM " . $this->getTable('productattachments/productatcats') . " WHERE left_node BETWEEN @myLeft AND @myRight");
            $db->exec("UPDATE " . $this->getTable('productattachments/productatcats') . " SET right_node = right_node - @myWidth WHERE right_node > @myRight");
            $db->exec("UPDATE " . $this->getTable('productattachments/productatcats') . " SET left_node = left_node - @myWidth WHERE left_node > @myRight");
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception($e);
        }
    }

    /**
     *
     * @Delete a node and all its children
     *
     * @access public
     *
     * @param string $node_id
     *
     */
    public function deleteNodeRecursive($node_id) {

        $db = $this->_getWriteAdapter();
        try {
            $db->beginTransaction();
            $db->exec("DELETE FROM " . $this->getTable('productattachments/productatcats') . " WHERE category_id = $node_id");
            $db->exec("DELETE FROM " . $this->getTable('productattachments/productattachments_category_link') . " WHERE category_id = $node_id");
            $db->exec("DELETE FROM " . $this->getTable('productattachments/productattachments_category_store') . " WHERE category_id = $node_id");
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception($e);
        }
    }

    /**
     *
     * @Change a node status and all its children
     *
     * @access public
     *
     * @param string $node_id,$status
     *
     */
    public function setNodeStatusRecursive($node_id, $status) {

        $db = $this->_getWriteAdapter();
        try {
            $db->beginTransaction();
            $db->exec("UPDATE " . $this->getTable('productattachments/productatcats') . " SET category_status = $status WHERE category_id = $node_id");
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception($e);
        }
    }

}
