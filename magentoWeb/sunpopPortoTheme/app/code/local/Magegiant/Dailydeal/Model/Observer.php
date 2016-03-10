<?php

class Magegiant_Dailydeal_Model_Observer
{
	public function collectionLoadAfter($observer)
	{
		$productCollection = $observer['collection'];
		foreach ($productCollection as $product) {
			$deal = Mage::getModel('dailydeal/dailydeal')->getDealByProduct($product->getEntityId());
			if ($deal->getId()) {
				$temp = $deal->getQuantity() - $deal->getSold();
				$product->setData('final_price', $product->getPrice() - $deal->getSave() * $product->getPrice() / 100);
			}
		}

	}

	public function getFinalPrice($observer)
	{
		$product   = $observer['product'];
		$deal = Mage::getModel('dailydeal/dailydeal')->getDealByProduct($product->getEntityId());
		if ($deal->getId()) {
			$temp = $deal->getQuantity() - $deal->getSold();
			$product->setData('final_price', $product->getPrice() - $deal->getSave() * $product->getPrice() / 100);
		}

	}

	public function quoteSubmitBefore($observer)
	{
		$order = $observer['order'];
		$items = $order->getAllItems();
		$deals = array();
		foreach ($items as $item) {
			$productId = $item->getProductId();
			$deal = Mage::getModel('dailydeal/dailydeal')->getDealByProduct($productId);
			if ($deal->getId()) {
				$temp = $deal->getQuantity() - $deal->getSold();
				$sold = $deal->getSold();
				if ($temp > 0) {
					$deals[] = $deal->getId();
					$deal->setSold($sold + $item->getQtyOrdered())
						->save();
				}
			}
		}
		$order->setData('dailydeals', implode(",", $deals));
	}

	public function checkOutSaveAfter($observer)
	{
		$cart  = $observer['cart'];
		$items = $cart->getQuote()->getAllItems();
		$temp  = Mage::getStoreConfig('dailydeal/general/limit');
		$i     = 0;
		foreach ($items as $item) {
			$deal = Mage::getModel('dailydeal/dailydeal')->getDealByProduct($item->getProductId());
			if ($deal->getId()) {
				$limit = $deal->getQuantity() - $deal->getSold();
				if ($limit > 0) {
					if (($limit > $temp) && ($temp > 0)) $limit = $temp;
					if ($item->getQty() > $limit) {
						$item->setQty($limit)->save();
						$i = 1;
					}
				}
			}
		}
		if ($i == 1)
			Mage::getSingleton('checkout/session')->addError(Mage::helper('dailydeal')->__('The number that you have inserted is over the deal quantity left. Please reinsert another one!'));
	}

	public function checkoutCartAddProduct()
	{
		$cart      = $this->_getCart();
		$items     = $cart->getQuote()->getAllItems();
		$productId = (int)Mage::app()->getRequest()->getParam('product');

		$deal = Mage::getModel('dailydeal/dailydeal')->getDealByProduct($productId);
		if ($deal->getId()) {
			$limit = $deal->getQuantity() - $deal->getSold();
			if ($limit > 0) {
				$temp = Mage::getStoreConfig('dailydeal/general/limit');
				if (($limit > $temp) && ($temp > 0)) $limit = $temp;
				$qty      = 1;
				$is_order = false;
				if (Mage::app()->getRequest()->getParam('qty')) $qty = Mage::app()->getRequest()->getParam('qty');

				foreach ($items as $item) {
					if ($item->getProductId() == $productId) {
						$is_order = true;
						if (($item->getQty() + $qty) > $limit) {
							Mage::app()->getRequest()->setPost('qty', 0);
							$item->setQty($limit - 1)->save();
							Mage::getSingleton('checkout/session')->addError(Mage::helper('dailydeal')->__('The number that you have inserted is over the deal quantity left. Please reinsert another one!'));
						}
					}
				}
				if ((!$is_order) && ($qty > $limit)) {
					Mage::app()->getRequest()->setPost('qty', $limit);
					Mage::getSingleton('checkout/session')->addError(Mage::helper('dailydeal')->__('The number that you have inserted is over the deal quantity left. Please reinsert another one!'));
				}
			}
		}
	}

	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}

	public function qtyItem($items, $product_id, $check)
	{
		$qty = 0;
		foreach ($items as $item) {
			if ($product_id == $item->getProductId()) {
				if ($check == 1) {
					$qty = $item->getQtyCanceled();
				} else {
					$qty = $item->getQty();
				}

				return $qty;
			}
		}

		return $qty;
	}

	public function refundCreditmemo($observer)
	{
		$creditmemo     = $observer['creditmemo'];
		$order_id       = $creditmemo->getOrderId();
		$order          = Mage::getModel('sales/order')->load($order_id);
		$deals     = $order->getDailydeals();
		$deals_arr = explode(',', $deals);
		$items          = $creditmemo->getAllItems();
		foreach ($deals_arr as $value) {
			$deal  = Mage::getModel('dailydeal/dailydeal')->load($value);
			$product_id = $deal->getProductId();
			$qty        = $this->qtyItem($items, $product_id, 0);
			$sold       = $deal->getSold() - $qty;

			if ($sold >= 0) {
				$deal->setSold($sold)->save();
			}
		}

	}

	public function orderCancelAfter($observer)
	{
		$order          = $observer['order'];
		$deals     = $order->getDailydeals();
		$items          = $order->getAllItems();
		$deals_arr = explode(',', $deals);
		foreach ($deals_arr as $value) {
			$deal  = Mage::getModel('dailydeal/dailydeal')->load($value);
			$product_id = $deal->getProductId();
			$qty        = $this->qtyItem($items, $product_id, 1);
			$sold       = $deal->getSold() - $qty;
			if ($sold >= 0) {
				$deal->setSold($sold)->save();

			}

		}
	}
}
