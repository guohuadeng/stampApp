<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Usa
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract USA shipping carrier model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
/**
 * Magento Webshopapps Module
 *
 * @category   Webshopapps
 * @package    Webshopapps Wsacommon
 * @copyright  Copyright (c) 2011 Zowta Ltd (http://www.webshopapps.com)
 * @license    www.webshopapps.com/license/license.txt
 * @author     Karen Baker <sales@webshopapps.com>
*/

abstract class Webshopapps_Wsacommon_Model_Shipping_Carrier_Baseabstract extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_debug;
    
    protected $_request = null;

    protected $_result = null;

    protected $_rawRequest = null;
    protected $_modName   = 'none';
    
   	protected $_code;

    /**
     * Part of carrier xml config path
     *
     * @var string
     */
    protected $_availabilityConfigField = 'active';
   	
   	protected static $_quotesCache = array();
   	
   	const XML_PATH_STORE_ADDRESS1     = 'shipping/origin/street_line1';
	const XML_PATH_STORE_ADDRESS2     = 'shipping/origin/street_line2';
	const XML_PATH_STORE_CITY         = 'shipping/origin/city';
	const XML_PATH_STORE_REGION_ID    = 'shipping/origin/region_id';
	const XML_PATH_STORE_ZIP          = 'shipping/origin/postcode';
	const XML_PATH_STORE_COUNTRY_ID   = 'shipping/origin/country_id';
   	
    
   	abstract protected  function _getQuotes();
 	abstract public function getCode($type, $code='');
    
    public function getTrackingInfo($tracking, $postcode=null, $orderId=null)
    {
        $info = array();

        $result = $this->getTracking($tracking);

        if($result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        }
        elseif (is_string($result) && !empty($result)) {
            return $result;
        } else {
            $info['title'] = $this->getConfigData('title');
            $info['number'] = $tracking;
            return $info;
        }

        return false;
    }

    /**
     * Check if carrier has shipping tracking option available
     * All Mage_Usa carriers have shipping tracking option available
     *
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return true;
    }

  	public function getResult()
    {
       return $this->_result;
    }
    
 	/**
     * Enter description here...
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        } 
        if ($this->_modName == 'none') {
        	$this->_debug = $this->getConfigData('debug');
        } else {
        	$this->_debug = Mage::helper('wsalogger')->isDebug($this->_modName);
        }
        
        
        $this->setRequest($request);
        
        $this->_result = $this->_getQuotes();
        
        $this->_updateFreeMethodQuote($request);

        return $this->getResult(); 
    }
    
    
 	protected function _setFreeMethodRequest($freeMethod)
    {
    	$this->_rawRequest->setIgnoreFreeItems(true);
    }
    
    public function getAllowedMethods()
    {
        return array($this->_code=>$this->getConfigData('name'));
    }  
    
     /**
     * Returns cache key for some request to carrier quotes service
     *
     * @param string|array $requestParams
     * @return string
     */
    protected function _getQuotesCacheKey($requestParams)
    {
        if (is_array($requestParams)) {
            $requestParams = implode(',', array_merge(
                array($this->getCarrierCode()),
                array_keys($requestParams),
                $requestParams)
            );
        }
        return crc32($requestParams);
    }
    
 /**
     * Checks whether some request to rates have already been done, so we have cache for it
     * Used to reduce number of same requests done to carrier service during one session
     *
     * Returns cached response or null
     *
     * @param string|array $requestParams
     * @return null|string
     */
    protected function _getCachedQuotes($requestParams)
    {
        $key = $this->_getQuotesCacheKey($requestParams);
        return isset(self::$_quotesCache[$key]) ? self::$_quotesCache[$key] : null;
    }

    /**
     * Sets received carrier quotes to cache
     *
     * @param string|array $requestParams
     * @param string $response
     * @return Mage_Usa_Model_Shipping_Carrier_Abstract
     */
    protected function _setCachedQuotes($requestParams, $response)
    {
        $key = $this->_getQuotesCacheKey($requestParams);
        self::$_quotesCache[$key] = $response;
        return $this;
    }


    /**
     * Get carrier by its code
     *
     * @param string $carrierCode
     * @param null|int $storeId
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getCarrierByCode($carrierCode, $storeId = null)
    {
        if (!Mage::getStoreConfigFlag('carriers/'.$carrierCode.'/'.$this->_availabilityConfigField, $storeId)) {
            return false;
        }
        $className = Mage::getStoreConfig('carriers/'.$carrierCode.'/model', $storeId);
        if (!$className) {
            return false;
        }
        $obj = Mage::getModel($className);
        if ($storeId) {
            $obj->setStore($storeId);
        }
        return $obj;
    }


    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _getQuote()
    {
        if (Mage::registry('rule_data')) {
            return $this->_getAdminQuote();

        }
        return $this->_getCart()->getQuote();


    }

    protected function _getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    protected function _getAdminQuote()
    {
        return $this->_getAdminSession()->getQuote();
    }
}
