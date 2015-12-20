<?php
class Sunpop_AutoWishlist_Model_Observer
{
	public function addWishlist(Varien_Event_Observer $observer)
	{ 
		if(Mage::helper('autowishlist')->isEnabled()){
			$order = $observer->getOrder();
			$items = $order->getAllVisibleItems();
			$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($order->getCustomerId(), true);
			foreach($items as $item){
			   $option = $item->getProductOptions();
			   $product = Mage::getModel('catalog/product')->load($item->getProductId());
			   $params = array('product' => $productId,
					'qty' => 1,
					'store_id' => $storeId,
					'options' => $option['info_buyRequest']['options']
				);
			$request = new Varien_Object();
			$request->setData($params);
			$result = $wishlist->addNewItem($product, $request,true);
			} 
			$wishlist->save(); 
		}
	}
		
}

