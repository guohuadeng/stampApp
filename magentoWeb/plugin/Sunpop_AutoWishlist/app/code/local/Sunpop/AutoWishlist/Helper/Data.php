<?php
class Sunpop_AutoWishlist_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_ENABLED       = 'autowishlist/general/enabled';
	
	public function isEnabled($store = null){
		return Mage::getStoreConfig(self::XML_PATH_ENABLED, $store);
	}
}
	 