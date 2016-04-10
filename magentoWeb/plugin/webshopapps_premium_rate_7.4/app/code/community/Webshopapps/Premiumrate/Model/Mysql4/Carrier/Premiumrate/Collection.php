<?php
/**
 * Magento Webshopapps Shipping Module
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
 * Shipping MatrixRates
 *
 * @category   Webshopapps
 * @package    Webshopapps_Premiumrate
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt
 * @author     Karen Baker <sales@webshopapps.com>
*/
class Webshopapps_Premiumrate_Model_Mysql4_Carrier_Premiumrate_Collection extends Varien_Data_Collection_Db
{
    protected $_shipTable;
    protected $_countryTable;
    protected $_regionTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('shipping_read'));
        $this->_shipTable = Mage::getSingleton('core/resource')->getTableName('premiumrate_shipping/premiumrate');
        $this->_countryTable = Mage::getSingleton('core/resource')->getTableName('directory/country');
        $this->_regionTable = Mage::getSingleton('core/resource')->getTableName('directory/country_region');
        $this->_select->from(array("s" => $this->_shipTable))
            ->joinLeft(array("c" => $this->_countryTable), 'c.country_id = s.dest_country_id', 'iso3_code AS dest_country')
            ->joinLeft(array("r" => $this->_regionTable), 'r.region_id = s.dest_region_id', 'code AS dest_region')
            ->order(array("dest_country", "dest_region", "dest_zip"));
        $this->_setIdFieldName('pk');
        return $this;
    }

    public function setWebsiteFilter($websiteId)
    {
        $this->_select->where("website_id = ?", $websiteId);

        return $this;
    }

    public function setConditionFilter($conditionName)
    {
        $this->_select->where("condition_name = ?", $conditionName);

        return $this;
    }

    public function setCountryFilter($countryId)
    {
        $this->_select->where("dest_country_id = ?", $countryId);

        return $this;
    }
    
	public function setRegionFilter($regionId)
    {
        $this->_select->where("dest_region_id = ?", $regionId);

        return $this;
    }
    
	public function setCityFilter($city)
    {
        $this->_select->where("STRCMP(LOWER(dest_city),LOWER(?)) = 0", $city);

        return $this;
    }
    

   	public function setPostcodeFilter($postcode)
    {
    	
    	$zipCodeRangeFiltering = Mage::getStoreConfig('carriers/premiumrate/zip_range');
    	if ($zipCodeRangeFiltering) {
        	$this->_select->where(" dest_zip <= ?", $postcode);
        	$this->_select->where(" dest_zip_to >= ?", $postcode);
    	} else {
        	$this->_select->where(" ? LIKE dest_zip", $postcode);
    	}

        return $this;
    }
    
	public function setDistinctDeliveryTypeFilter() {
    	
    	$this->_select->reset(Zend_Db_Select::COLUMNS);
    	$this->_select->reset(Zend_Db_Select::ORDER);
    	$this->_select->distinct(true);
    	$this->_select->columns('delivery_type');
    	$this->_select->order('delivery_type');
        return $this;
    }
    
    public function setWeightRange($weight)
    {
    	$this->_select->where('weight_from_value<?', $weight);
		$this->_select->where('weight_to_value>=?', $weight);
        return $this;
    }
    
    
	public function setDistinctDeliveryCodeFilter() {
    	
    	$this->_select->reset(Zend_Db_Select::COLUMNS);
    	$this->_select->reset(Zend_Db_Select::ORDER);
    	$this->_select->distinct(true);
    	$this->_select->columns('delivery_type');
    	$this->_select->columns('algorithm');
    	$this->_select->order('delivery_type');
        return $this;
    }
}