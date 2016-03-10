<?php

class Magegiant_Dailydeal_Block_Adminhtml_Dailydeal_Renderer_Productavailable extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$str       = '';
		$productId = $row->getProductId();
		$product   = Mage::getModel('catalog/product')->load($productId);
		if (!$productId) {
			$str = 'There is not product available !';
		} else {
			$str .= '<a href="' . $this->getUrl('adminhtml/catalog_product/edit', array('id' => $product->getEntityId())) . '">' . $product->getName() . '</a></br>';
		}

		return $str;
	}
}