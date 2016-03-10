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
class Smartwave_Filterproducts_Block_Sale_Home_List extends Smartwave_Filterproducts_Block_Sale_List
{
    
    protected function _getProductCollection()
    {
        $category_id = $this->getCategoryId();
        
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $tomorrow = mktime(0, 0, 0, date('m'), date('d')+1, date('y'));
        $dateTomorrow = date('m/d/y', $tomorrow);
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
        
        if($category_id) {
            $category = Mage::getModel('catalog/category')->load($category_id);    
            
            $collection = $this->_addProductAttributesAndPrices($collection)
             ->addCategoryFilter($category)
             ->addStoreFilter()
             ->addAttributeToSort('entity_id', 'desc') //<b>THIS WILL SHOW THE LATEST PRODUCTS FIRST</b>
             ->addAttributeToFilter('special_price', array('gt'=> -1))
             ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
             ->addAttributeToFilter('special_to_date', array('or'=> array(0 => array('date' => true, 'from' => $dateTomorrow), 1 => array('is' => new Zend_Db_Expr('null')))), 'left')
             ->setPageSize($this->getProductCount());
        }else{
            $collection = $this->_addProductAttributesAndPrices($collection)
             ->addStoreFilter()
             ->addAttributeToSort('entity_id', 'desc') //<b>THIS WILL SHOW THE LATEST PRODUCTS FIRST</b>
             ->addAttributeToFilter('special_price', array('gt'=> -1))
             ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
             ->addAttributeToFilter('special_to_date', array('or'=> array(0 => array('date' => true, 'from' => $dateTomorrow), 1 => array('is' => new Zend_Db_Expr('null')))), 'left')
             ->setPageSize($this->getProductCount());
        }
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if(!Mage::getStoreConfig("cataloginventory/options/show_out_of_stock", $code))
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection); 

		$this->_productCollection = $collection;
        return $this->_productCollection;
    }
    public function getToolbarHtml()
    {
    
    }

}
?>