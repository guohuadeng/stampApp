<?php

class Magegiant_Dailydeal_Model_Dailydeal extends Mage_Core_Model_Abstract
{
	protected $_arrayfilter = null;
	protected $_dealsCollection = null;

	public function _construct()
	{
		parent::_construct();
		$this->_init('dailydeal/dailydeal');
	}

	public function getDailydeals()
	{
        
        $expired_deals                  = $this->getCollection()
                ->addFieldToFilter('status', 3);
        $current_time = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
        $expired_deals->getSelect()->where('(close_time <= ?)', $current_time);
        
        foreach($expired_deals as $entry){
            $entry['status'] = 4;
            $entry->save();
        }
        
		if (is_null($this->_dealsCollection)) {
			$store                  = Mage::app()->getStore()->getStoreId();
			$deals                  = $this->getCollection()
				->addFieldToFilter('status', 3)
				->addFieldToFilter('is_random', 0)
				->addFieldToFilter('store_id', $this->getArrayFilter($store))
				->addFieldToFilter('product_id', array('nin' => 0));
            $deals->getSelect()->where('(start_time <= ?)', $current_time);
			$this->_dealsCollection = $deals;
		}
        
		return $this->_dealsCollection;
	}

	public function getLoadedProductCollection($store = null)
	{

		$deals = $this->getCollection()
			->addFieldToFilter('status', 3)
			->addFieldToFilter('is_random', 0)
			->addFieldToFilter('product_id', array('nin' => 0));
		if ($store != 0)
			$deals->addFieldToFilter('store_id', $this->getArrayFilter($store));
		$productIds = array();
		foreach ($deals as $deal) {
			if ($deal->getQuantity() > $deal->getSold())
				$productIds[] = $deal->getProductId();
		}
		$products = Mage::getModel('catalog/product')->getCollection()
			->addFieldToFilter('entity_id', array('in' => $productIds))
			->addAttributeToSelect('*');

		return $products;
	}

	public function getDealIds()
	{
		$dealIds        = $this->getDailydeals()->getAllProductIds();
		$currentProduct = Mage::registry('current_product');
		if ($currentProduct) {
			if (in_array($currentProduct->getId(), $dealIds)) {
				unset($dealIds[$currentProduct->getId()]);
			}
		}
		$dealIds = array_unique($dealIds);

		return $dealIds;
	}

	public function randomArray($array)
	{
		shuffle($array);

		return $array;
	}


	public function getLeftSidebar()
	{
		$config = Mage::helper('dailydeal')->getConfig();
		$limit  = $config->getLeftLimit();
		$method = $config->getLeftDisplayMethod();

		return $this->getSidebar($limit, $method);
	}

	public function getRightSidebar()
	{
		$config = Mage::helper('dailydeal')->getConfig();
		$limit  = $config->getRightLimit();
		$method = $config->getRightDisplayMethod();

		return $this->getSidebar($limit, $method);
	}


	public function getSidebar($limit = 3, $method = '')
	{
		$dealIds = $this->getDealIds();
		try {
			if ($_product = Mage::registry('current_product')) {
				if (($key = array_search($_product->getId(), $dealIds)) !== false) {
					unset($dealIds[$key]);
				}
			}
		} catch (Exception $e) {
		}

		switch ($method) {
			case Magegiant_Dailydeal_Model_System_Config_Display::RANDOM:
				$dealIds = $this->randomArray($dealIds);
				$dealIds = array_slice($dealIds, 0, $limit);
				$deals   = Mage::getModel('catalog/product')->getCollection()
					->addFieldToFilter('entity_id', array('in' => $dealIds))->addAttributeToSelect('*');

				break;
			case Magegiant_Dailydeal_Model_System_Config_Display::TODAY:
				$dealIds = $this->_getDealsFromDate('today');

				$deals = Mage::getModel('catalog/product')->getCollection();
				$deals->addFieldToFilter('entity_id', array('in' => $dealIds))->addAttributeToSelect('*');
				$deals->getSelect()->limit($limit);
				$deals->setOrder('id', 'DESC');
				break;

			case Magegiant_Dailydeal_Model_System_Config_Display::THIS_WEEK:


				$dealIds = $this->_getDealsFromDate('this week');

				$deals = Mage::getModel('catalog/product')->getCollection();
				$deals->addFieldToFilter('entity_id', array('in' => $dealIds))->addAttributeToSelect('*');
				$deals->getSelect()->limit($limit);
				$deals->setOrder('id', 'DESC');
				break;

			case Magegiant_Dailydeal_Model_System_Config_Display::THIS_MONTH:


				$dealIds = $this->_getDealsFromDate('this month');

				$deals = Mage::getModel('catalog/product')->getCollection();
				$deals->addFieldToFilter('entity_id', array('in' => $dealIds))->addAttributeToSelect('*');
				$deals->getSelect()->limit($limit);
				$deals->setOrder('id', 'DESC');
				break;


			default:
				$deals = Mage::getModel('catalog/product')->getCollection();
				$deals->addFieldToFilter('entity_id', array('in' => $dealIds))->addAttributeToSelect('*');
				$deals->getSelect()->limit($limit);
				$deals->setOrder('id', 'DESC');
				break;
		}


		return $deals;
	}


	public function getDealByProduct($productId)
	{
		$deal = $this->getDailydeals()
			->addFieldToFilter('product_id', $productId);
        $current_time = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
        $deal->getSelect()->where('(start_time <= ?)', $current_time);
		if ($deal->getSize())
			return $deal->getFirstItem();

		return Mage::getModel('dailydeal/dailydeal');
	}

	public function isExistDeal($deal)
	{
		if (!$deal->getIsRandom()) return false;
		$_deal = $this->getCollection()
			->addFieldToFilter('status', 3)
			->addFieldToFilter('is_random', 0)
			->addFieldToFilter('product_id', $deal->getProductId())
			->getFirstItem();

		if (!$_deal OR !$_deal->getId()) return false;

		return ($_deal->getId() == $deal->getId());
	}

	public function getLimit($dealId)
	{
		$quantity    = $this->load($dealId)->getQuantity();
		$collection1 = Mage::getResourceModel('sales/order_collection')
			->addFieldToFilter('dailydeals', array('finset' => $dealId));
		$temp        = $quantity - $collection1->getSize();

		return $temp;
	}

	public function getArrayFilter($store)
	{
		if (is_null($this->_arrayfilter)) {
			$arr   = explode(',', $store);
			$array = array();
			if ($store != 0)
				foreach ($arr as $a) {
					$array[] = array('finset' => $a);
				}
			$array[]            = array('finset' => 0);
			$this->_arrayfilter = $array;
		}

		return $this->_arrayfilter;
	}

	public function getDateByString($string)
	{
		return date("Y-m-d", Mage::getModel('core/date')->timestamp(strtotime($string)));

	}


	protected function _getDealsFromDate($date = 'today')
	{

		$dailyDeals = $this->getCollection()
			->addFieldToFilter('status', 3)
			->addFieldToFilter('is_random', 0)
			->addFieldToFilter('store_id', $this->getArrayFilter(Mage::app()->getStore()->getStoreId()))
			->addFieldToFilter('product_id', array('nin' => 0));

		$date = $this->getDateByString($date);
//		$dailyDeals->getSelect()->where('(start_time IS NULL) OR (date(start_time) <= date(?))', $date);
		$dailyDeals->getSelect()->where('(date(close_time) <= date(?))', $date);

		$dealIds = $dailyDeals->getAllProductIds();

		return $dealIds;
	}
}