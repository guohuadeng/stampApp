<?php
class Smartwave_Ajaxcatalog_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_optionsMap;
	protected $_params = null;

    public function getConfigData($node) 
	{
        return Mage::getStoreConfig('infinitescroll/' . $node);
    }

	public function isMemoryActive()
	{
		return $this->getConfigData('memory/enabled');
	}
	
	public function getNextPageNumber()
	{
		return Mage::app()->getRequest()->getParam('p');
	}
	
	public function getSession()
	{
		return Mage::getSingleton("core/session");
	}
	


	public function isEnabled()
	{
		return Mage::getStoreConfig('infinitescroll/general/enabled');
	}


	public function getCurrentPageType()
	{
		// TODO: we could do this with the full path to the request directly
		$where = 'grid';
		/** @var Mage_Catalog_Model_Category $currentCategory */
		$currentCategory = Mage::registry('current_category');
		if ($currentCategory) {
			$where = "grid";
			if($currentCategory->getIsAnchor()){
				$where = "layer";
			}
		}
		$controller = Mage::app()->getRequest()->getControllerName();
		if ( $controller == "result"){ $where = "search"; }
		else if ( $controller == "advanced") { $where = "advanced"; }
		return $where;
	}

	/**
	 * check general and instance enable
	 * @return bool
	 */
	public function isEnabledInCurrentPage()
	{
		$pageType = $this->getCurrentPageType();
		return $this->isEnabled() && Mage::getStoreConfig('infinitescroll/instances/'.$pageType);
	}

	public function getParam($key) {
        $params = $this->getParams();
        return isset($params[$key]) ? $params[$key] : null;
    }

    public function getParams($asString = false, $without = null) {
        if (is_null($this->_params)) {

            $session = Mage::getSingleton('catalog/session');
            $needClearAll = false;

            $currentCurrencyRate = $session->getCurrentCurrencyRate();
            $currencyRate = Mage::app()->getStore()->convertPrice(1000000, false);
            $currencyRate = $currencyRate / 1000000;

            $needClearPrice = false;

            if ($currentCurrencyRate AND $currentCurrencyRate != $currencyRate) {
                $needClearPrice = true;
            }

            if ($needClearPrice) {
                $sess = (array) $session->getLayerParams();
                if ($sess) {
                    $defaultQueryKeys = $this->getDefaultQueryKeys();
                    foreach ($sess as $sKey => $sVal) {
                        if (!in_array($sKey, $defaultQueryKeys)) {
                            $attribute = Mage::getModel('eav/entity_attribute');
                            $attribute->load($sKey, 'attribute_code');
                            if ($attribute->getFrontendInput() == 'price') {
                                unset($sess[$sKey]);
                            }
                        }
                    }
                    $session->setLayerParams($sess);
                }
            }

            $session->setCurrentCurrencyRate($currencyRate);

            $query = Mage::app()->getRequest()->getQuery();
            $sess = (array) $session->getLayerParams();
            $this->_params = array_merge($sess, $query);

            if (!empty($query['clearall']) OR $needClearAll) {
                $this->_params = array();
            }
            $sess = array();
            foreach ($this->_params as $key => $value) {
                if ($value && 'clear' != $value)
                    $sess[$key] = $value;
            }

            if (Mage::registry('new_category') AND isset($sess['p'])) {
                //unset($sess['p']);
                $sess = array();
            }

            $session->setLayerParams($sess);
            $this->_params = $sess;

            Mage::register('current_session_params', $sess);
        }

        if ($asString) {
            return $this->toQuery($this->_params, $without);
        }

        return $this->_params;
    }

	public function toQuery($params, $without = null) {
        if (!is_array($without))
            $without = array($without);

        $queryStr = '?';
        foreach ($params as $k => $v) {
            if (strpos($k, "amp;") !== false)
                continue;

            if (!in_array($k, $without))
                $queryStr .= $k . '=' . urlencode($v) . '&';
        }
        return substr($queryStr, 0, -1);
    }

	public function getCacheKey($attrCode) {
        $defaultQueryKeys = $this->getDefaultQueryKeys();
        $defaultQueryKeys[] = $attrCode;
        return md5($this->getParams(true, $defaultQueryKeys));
    }

	protected function getDefaultQueryKeys() {
        return array('x', 'y', 'mode', 'p', 'order', 'dir', 'limit', 'q', '___store', '___from_store', 'sns');
    }
}
	 