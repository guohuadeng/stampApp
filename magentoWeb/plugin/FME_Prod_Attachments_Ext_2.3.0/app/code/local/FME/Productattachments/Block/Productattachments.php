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
class FME_Productattachments_Block_Productattachments extends Mage_Catalog_Block_Product_Abstract {

    const DISPLAY_CONTROLS = 'productattachments/productattachments/enabled';
	/*
    protected function _tohtml() {
        if (!Mage::getStoreConfig(self::DISPLAY_CONTROLS))
            return parent::_toHtml();

        $this->setLinksforProduct();
        //$this->setTemplate("productattachments/productattachments.phtml");
        return parent::_toHtml();
    }
*/
    public function getProductRelatedAttachments($blockPosition = null, $categoryId = null) {

        $id = $this->getProduct()->getId();
        $productattachmentsTable = Mage::getSingleton('core/resource')->getTableName('productattachments');
        $productattachmentsProductsTable = Mage::getSingleton('core/resource')->getTableName('productattachments_products');
        $productattachmentsStoreTable = Mage::getSingleton('core/resource')->getTableName('productattachments_store');
        $productattachmentsCategoryTable = Mage::getSingleton('core/resource')->getTableName('productattachments_cats');
        $storeId = Mage::app()->getStore()->getId();

        $collection = Mage::getModel('productattachments/productattachments')
                ->getCollection()
                ->addStoreFilter($storeId);

        $collection->getSelect()
                ->join(array('pastore' => $productattachmentsStoreTable), 'main_table.productattachments_id = pastore.productattachments_id')
                ->join(array('paproduct' => $productattachmentsProductsTable), 'main_table.productattachments_id = paproduct.productattachments_id')
                ->join(array('pacat' => $productattachmentsCategoryTable), 'main_table.cat_id = pacat.category_id')
                ->where('paproduct.product_id = (?)', $id)
                ->where('pastore.store_id in (?)', array(0, $storeId));
        if ($blockPosition != null) {
            $collection->getSelect()
                    ->where('main_table.block_position LIKE ?', '%' . $blockPosition . '%');
        }
        if ($categoryId != null) {
            $collection->getSelect()
                    ->where('main_table.cat_id = (?)', $categoryId)
                    ->where('pacat.category_status = (?)', 1);
        }

        $collection->getSelect()
                ->where('main_table.status = (?)', 1); //echo (string) $collection->getSelect();

        return $collection;
    }

    public function getBlockTitle() {

        $_res = Mage::getSingleton('core/resource');

        $table = $_res->getTableName('productattachments_products');
        $_read = $_res->getConnection('core_read');

        $q = $_read->select()
                ->from(array('prod' => $table), 'block_name_product')
                ->where('prod.product_id = (?)', $this->getProduct()->getId());

        return $_read->fetchOne($q);
    }
    
    

}
