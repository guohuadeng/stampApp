<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of List
 *
 * @author om
 */
class Smartwave_Filterproducts_Block_Latest_Home_List extends Smartwave_Filterproducts_Block_Latest_List
{
		protected function _getProductCollection()
    	{
        
        $category_id = $this->getCategoryId();
        
        $storeId    = Mage::app()->getStore()->getId();
        $products = Mage::getResourceModel('catalog/product_collection');
        if($category_id) {
            $category = Mage::getModel('catalog/category')->load($category_id);    
            
            $products = $this->_addProductAttributesAndPrices($products)
            ->addAttributeToSort('created_at', 'desc')
            ->addCategoryFilter($category)
            ->setStoreId($storeId)
            ->addStoreFilter($storeId);
        }
        else {
            $products = $this->_addProductAttributesAndPrices($products)
            ->addAttributeToSort('created_at', 'desc')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId);
        }

        $product_count = $this->getProductCount();
            
        if($product_count)
        {
            $products->setPageSize($product_count);
        }


        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

        $this->_productCollection = $products;

        return $this->_productCollection;
    	}
		
		public function getToolbarHtml()
    	{
        
    	}
}