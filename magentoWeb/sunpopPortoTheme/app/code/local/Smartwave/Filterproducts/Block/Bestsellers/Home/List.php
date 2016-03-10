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
class Smartwave_Filterproducts_Block_Bestsellers_Home_List extends Smartwave_Filterproducts_Block_Bestsellers_List
{

	protected function _getProductCollection()
    {
        
        $storeId = Mage::app()->getStore()->getId();
        $category_id = $this->getCategoryId();
        
        $products = Mage::getResourceModel('reports/product_collection')
                ->addAttributeToSelect('*');
                
        $adapter              = $products->getConnection();
        //$compositeTypeIds     = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');

        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),

        );

        $productJoinCondition = array(
            $adapter->quoteInto('(e.type_id NOT IN (?))', 'grouped'),
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', $products->getProductEntityTypeId())
        );

        $products->getSelect()->reset()
            ->from(
                array('order_items' => $products->getTable('sales/order_item')),
                array(
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name'
                ))
            ->joinInner(
                array('order' => $products->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array())
            ->joinLeft(
                array('e' => $products->getProductEntityTableName()),
                implode(' AND ', $productJoinCondition),
                array(
                    'entity_id' => 'order_items.product_id',
                    'entity_type_id' => 'e.entity_type_id',
                    'attribute_set_id' => 'e.attribute_set_id',
                    'type_id' => 'e.type_id',
                    'sku' => 'e.sku',
                    'has_options' => 'e.has_options',
                    'required_options' => 'e.required_options',
                    'created_at' => 'e.created_at',
                    'updated_at' => 'e.updated_at'
                ))
            ->where('parent_item_id IS NULL')
            ->group('order_items.product_id')
            ->having('SUM(order_items.qty_ordered) > ?', 0);
            
        if($category_id) {
        
            $category = Mage::getModel('catalog/category')->load($category_id);
        
            $products->addCategoryFilter($category);
        }

		$productFlatData = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
		if($productFlatData == "1")
		{
			$products->getSelect()->joinLeft(
	                array('flat' => 'catalog_product_flat_'.$storeId),
	                "(e.entity_id = flat.entity_id ) ",
	                //array(
//	                   'flat.name AS name','flat.image AS small_image','flat.price AS price','flat.minimal_price as minimal_price','flat.special_price as special_price','flat.special_from_date AS special_from_date','flat.special_to_date AS special_to_date'
//	                )
					array(
	                   'flat.name AS name','flat.small_image AS small_image','flat.price AS price','flat.special_price as special_price','flat.special_from_date AS special_from_date','flat.special_to_date AS special_to_date'
					)
	            );
		}
        $products->setStoreId($storeId)
                ->addStoreFilter($storeId)
                ->setOrder('ordered_qty', 'desc')
                ->setPageSize($this->getProductCount());
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if(!Mage::getStoreConfig("cataloginventory/options/show_out_of_stock", $code))
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products); 
            
        $this->_productCollection = $products;

        return $this->_productCollection;
    	}

		public function getToolbarHtml()
    	{

    	}
        
}