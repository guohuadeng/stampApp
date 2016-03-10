<?php

class Magegiant_Dailydeal_Block_Dailydeal extends Mage_Catalog_Block_Product_List

{
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	/**
	 * get collection dailydeal active
	 *
	 * @param
	 * @return Magegiant_Dailydeal_Model_Dailydeal $deals
	 */
	public function getDailydeals()
	{
		$deals = Mage::getModel('dailydeal/dailydeal')->getDailydeals();

		return $deals;
	}

	/**
	 * get dailydeal by product_id
	 *
	 * @param int $productId
	 * @return Magegiant_Dailydeal_Model_Dailydeal $deal
	 */
	public function getDealByProduct($productId)
	{
		$deal = Mage::getModel('dailydeal/dailydeal')->getDealByProduct($productId);

		return $deal;
	}

	/**
	 * get collection product from collection dailydeals active
	 *
	 * @param
	 * @return Magegiant_Catalog_Model_Product $this->_productCollection
	 */
	protected function _getProductCollection()
	{
		if (is_null($this->_productCollection)) {
			$productIds = array();
			$deals = Mage::getModel('dailydeal/dailydeal')->getDailydeals();
			foreach ($deals as $deal) {
				if ($deal->getQuantity() > $deal->getSold())
					$productIds[] = $deal->getProductId();
			}


			$this->_productCollection = Mage::getResourceModel('catalog/product_collection')
				->setStoreId($this->getStoreId())
				->addFieldToFilter('entity_id', array('in' => $productIds))
				->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
				->addMinimalPrice()
				->addTaxPercents()
				->addStoreFilter();

			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($this->_productCollection);

		}

		return $this->_productCollection;
	}

	public function getStoreId()
	{
		if ($this->hasData('store_id')) {
			return $this->_getData('store_id');
		}

		return Mage::app()->getStore()->getId();
	}
}