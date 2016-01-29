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
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 � free-magentoextensions.com All right reserved
 */
class FME_Productattachments_Model_Productattachments extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('productattachments/productattachments');
    }

    /**
     * Retrieve related Products
     *
     * @return array
     */
    public function getRelatedProducts($attachmentId) {

        $productattachmentsTable = Mage::getSingleton('core/resource')
                ->getTableName('productattachments_products');

        $collection = Mage::getModel('productattachments/productattachments')
                ->getCollection()
                ->addAttachmentIdFilter($attachmentId);


        $collection->getSelect()
                ->joinLeft(array('related' => $productattachmentsTable), 'main_table.productattachments_id = related.productattachments_id'
                )
                ->order('main_table.productattachments_id');
        //Mage::log((string)$collection->getSelect());
        return $collection->getData();
    }

    /**
     * Retrieve related Products
     *
     * @return array
     */
    public function getRelatedAttachments($productId) {

        $productattachmentsTable = Mage::getSingleton('core/resource')->getTableName('productattachments_products');
        $collection = Mage::getModel('productattachments/productattachments')->getCollection();
        $collection->getSelect()
                ->join(array('related' => $productattachmentsTable), 'main_table.productattachments_id = related.productattachments_id and related.product_id = ' . $productId
                )
                ->order('main_table.productattachments_id');
        return $collection->getData();
    }

    /**
     * Update Number of downloads counter
     *
     * @return array
     */
    public function updateCounter($id) {
        return $this->_getResource()->updateDownloadsCounter($id);
    }

    public function getCMSPage() {
        $CMSTable = Mage::getSingleton('core/resource')->getTableName('cms_page');
        $sqry = "select title as label, page_id as value from " . $CMSTable . " where is_active=1";
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select = $connection->query($sqry);
        return $rows = $select->fetchAll();
    }

    public function getAllCategoriesToArray() {
        
    }
}
