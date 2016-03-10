<?php

class Smartwave_Porto_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    protected $_texturePath;
    
    public function __construct()
    {
        $this->_texturePath = 'wysiwyg/porto/texture/default/';
    }

    public function getCfgGroup($group, $storeId = NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig('porto/' . $group, $storeId);
        else
            return Mage::getStoreConfig('porto/' . $group);
    }
    
    public function getCfgSectionDesign($storeId = NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig('porto_design', $storeId);
        else
            return Mage::getStoreConfig('porto_design');
    }

    public function getCfgSectionSettings($storeId = NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig('porto_settings', $storeId);
        else
            return Mage::getStoreConfig('porto_settings');
    }
    
    public function getTexturePath()
    {
        return $this->_texturePath;
    }

    public function getCfg($optionString)
    {
        return Mage::getStoreConfig('porto_settings/' . $optionString);
    }
     public function getImage($product, $imgWidth, $imgHeight, $imgVersion='small_image', $file=NULL) 
    {
        $url = '';
        if ($imgHeight <= 0)
        {
            $url = Mage::helper('catalog/image')
                ->init($product, $imgVersion, $file)
                //->constrainOnly(true)
                ->keepAspectRatio(true)
                //->setQuality(100)
                ->keepFrame(false)
                ->resize($imgWidth);
        }
        else
        {
            $url = Mage::helper('catalog/image')
                ->init($product, $imgVersion, $file)
                ->resize($imgWidth, $imgHeight);
        }
        return $url;
    }
    
    // get hover image for product
    public function getHoverImageHtml($product, $imgWidth, $imgHeight, $imgVersion='small_image') 
    {
        $product->load('media_gallery');
        $order = $this->getConfig('category/image_order');
        if ($gallery = $product->getMediaGalleryImages())
        {
            if ($hoverImage = $gallery->getItemByColumnValue('position', $order))
            {
                $url = '';
                if ($imgHeight <= 0)
                {
                    $url = Mage::helper('catalog/image')
                        ->init($product, $imgVersion, $hoverImage->getFile())
                        ->constrainOnly(true)
                        ->keepAspectRatio(true)
                        ->keepFrame(false)
                        ->resize($imgWidth);
                }
                else
                {
                    $url = Mage::helper('catalog/image')
                        ->init($product, $imgVersion, $hoverImage->getFile())
                        ->resize($imgWidth, $imgHeight);
                }
                return '<img class="hover-image" src="' . $url . '" alt="' . $product->getName() . '" />';
            }
        }
        
        return '';
    }
    public function getHomeUrl() {
        return array(
            "label" => $this->__('Home'),
            "title" => $this->__('Home Page'),
            "link" => Mage::getUrl('')
        );
    }
    public function getPreviousProduct()
    {
        $_prev_prod = NULL;
        $_product_id = Mage::registry('current_product')->getId();

        $cat = Mage::registry('current_category');
        if($cat) {
            $category_products = $cat->getProductCollection()->addAttributeToSort('position', 'asc');
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($category_products);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($category_products);

            $store = Mage::app()->getStore();
            $code = $store->getCode();
            if (!Mage::getStoreConfig("cataloginventory/options/show_out_of_stock", $code))
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($category_products);

            $items = $category_products->getItems();
            $cat_prod_ids = (array_keys($items));

            $_pos = array_search($_product_id, $cat_prod_ids); // get position of current product

            // get the next product url
            if (isset($cat_prod_ids[$_pos - 1])) {
                $_prev_prod = Mage::getModel('catalog/product')->load($cat_prod_ids[$_pos - 1]);
            } else {
                return false;
            }
        }
        if($_prev_prod != NULL){
            return $_prev_prod;
        } else {
            return false;
        }
 
    }
 
 
    public function getNextProduct()
    {
        $_next_prod = NULL;
        $_product_id = Mage::registry('current_product')->getId();

        $cat = Mage::registry('current_category');

        if($cat) {
            $category_products = $cat->getProductCollection()->addAttributeToSort('position', 'asc');
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($category_products);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($category_products);

            $store = Mage::app()->getStore();
            $code = $store->getCode();
            if (!Mage::getStoreConfig("cataloginventory/options/show_out_of_stock", $code))
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($category_products);

            $items = $category_products->getItems();
            $cat_prod_ids = (array_keys($items));

            $_pos = array_search($_product_id, $cat_prod_ids); // get position of current product

            // get the next product url
            if (isset($cat_prod_ids[$_pos + 1])) {
                $_next_prod = Mage::getModel('catalog/product')->load($cat_prod_ids[$_pos + 1]);
            } else {
                return false;
            }
        }

        if($_next_prod != NULL){
            return $_next_prod;
        } else {
            return false;
        }
    }
    public function getCompareUrl() {
        $_helper = Mage::helper("catalog/product_compare");
        return $_helper->getListUrl();
    }
    public function isEnabledonConfig($id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if(Mage::getStoreConfig("porto_settings/product_view_custom_tab/custom_tab",$code)){
            if(Mage::getStoreConfig("porto_settings/product_view_custom_tab/".$id,$code))
                return true;
        }
        return false;
    }
    public function isEnabledfromCategory(){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if(Mage::getStoreConfig("porto_settings/product_view_custom_tab/from_category",$code))
            return true;
        return false;
    }
    public function getTabIdField($type,$id){
        $num = substr($id,-1);
        $config_id = "";
        switch($type){
            case "attribute":
                $config_id = "attribute_tab_id_".$num;
                break;
            case "static_block":
                $config_id = "static_block_tab_id_".$num;
                break;
        }
        return $config_id;
    }
    public function isEnabledonParentCategory($attribute, $category){
        //$category = Mage::getModel("catalog/category")->load($category_id);
        if($category->getData($attribute) == "yes"){
            return true;
        }
        if($category->getData($attribute) == "no"){
            return false;
        }
        if(!$category->getData($attribute)){
            if($category->getId() == Mage::app()->getStore()->getRootCategoryId() || $category->getId() == 1){
                return true;
            }
            return $this->isEnabledonParentCategory($attribute, $category->getParentCategory());
        }
    }
    public function isEnabledonCategory($type, $id, $product_id){
        $product = Mage::getModel("catalog/product")->load($product_id);
        $attribute = "";
        if($type=="attribute"){
            $attribute = "sw_product_attribute_tab_".substr($id,-1);
        }else{
            $attribute = "sw_product_staticblock_tab_".substr($id,-1);
        }
        $category = $product->getCategory();
        if(!$category){
            $c = $product->getCategoryCollection()->addAttributeToSelect("*");
            $category = $c->getLastItem();
        }
        if(!$category->getId()){
            return false;
        }
        return $this->isEnabledonParentCategory($attribute, $category);
    }
    public function isEnabledTab($type, $id, $product_id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();

        if(!$this->isEnabledonConfig($id)){
            return false;
        }
        $config_id = Mage::getStoreConfig("porto_settings/product_view_custom_tab/".$this->getTabIdField($type,$id),$code);
        if(!$config_id)
            return false;
        if(!$this->getTabTitle($type, $id, $product_id))
            return false;
        if($this->isEnabledfromCategory()){
            if(!$this->isEnabledonCategory($type, $id, $product_id))
                return false;
        }
        return true;
    }
    public function getTabTitle($type, $id, $product_id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        $config_id = Mage::getStoreConfig("porto_settings/product_view_custom_tab/".$this->getTabIdField($type,$id),$code);
        $title = "";
        switch($type){
            case "attribute":
                $product = Mage::getModel("catalog/product")->load($product_id);
                $title = $product->getResource()->getAttribute($config_id)->getStoreLabel();
                if(!$product->getResource()->getAttribute($config_id)->getFrontend()->getValue($product))
                    $title = "";
                break;
            case "static_block":
                $block = Mage::getModel("cms/block")->setStoreId(Mage::app()->getStore()->getId())->load($config_id);
                $title = $block->getTitle();
                if(!$block->getIsActive())
                    $title = "";
                break;
        }
        return $title;
    }
    public function getTabContents($type, $id, $product_id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        $config_id = Mage::getStoreConfig("porto_settings/product_view_custom_tab/".$this->getTabIdField($type,$id),$code);
        $content = "";
        switch($type){
            case "attribute":
                $product = Mage::getModel("catalog/product")->load($product_id);
                $content = $product->getResource()->getAttribute($config_id)->getFrontend()->getValue($product);
				$proc_helper = Mage::helper('cms');
                $processor = $proc_helper->getPageTemplateProcessor();
                $content = $processor->filter($content);    
                break;
            case "static_block":
                $block = Mage::getModel("cms/block")->setStoreId(Mage::app()->getStore()->getId())->load($config_id);
                $content = $block->getContent(); 
                $proc_helper = Mage::helper('cms');
                $processor = $proc_helper->getPageTemplateProcessor();
                $content = $processor->filter($content);           
                break;
        }
        return $content;
    }
}
