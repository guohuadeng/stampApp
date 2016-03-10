<?php
class Magegiant_Dailydeal_Block_Adminhtml_Dailydeal_Renderer_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
                $productIds = $row->getProducts();
                $productIds=explode(",",$productIds);
                $products=Mage::getResourceModel('catalog/product_collection')
                        ->addFieldToFilter('entity_id', array('in'=>$productIds))
                        ->addAttributeToSelect('*');
                $str='';
                $i=1;
                foreach($products as $product ){
                    $str .= $i.'. <a href="'.$this->getUrl('adminhtml/catalog_product/edit', array('id' => $product->getEntityId())).'">'.$product->getName().'</a></br>';
                    $i++;
                }
		return $str;
	}
}