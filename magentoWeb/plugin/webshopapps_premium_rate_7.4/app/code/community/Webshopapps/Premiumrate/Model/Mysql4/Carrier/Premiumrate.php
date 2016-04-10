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
class Webshopapps_Premiumrate_Model_Mysql4_Carrier_Premiumrate extends Mage_Core_Model_Mysql4_Abstract
{

	private $_stockFound = false;
	private $_outofstock = false;
	private $_twoPhaseFiltering;
	private $_shortMatchPostcode = '';
	private $_longMatchPostcode = '';
	private $_table;
	private $_request;
	private $_zipSearchString;
	private $_debug;
	private $_alwaysUseWeight;
	private $_excludedDeliveries;


    protected function _construct()
    {
        $this->_init('shipping/premiumrate', 'pk');
    }

    public function getNewRate(Mage_Shipping_Model_Rate_Request $request)
    {
        $read = $this->_getReadAdapter();
        $this->_table = Mage::getSingleton('core/resource')->getTableName('premiumrate_shipping/premiumrate');
        $this->_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Premiumrate');
		$usingGreaterVolLogic=Mage::getStoreConfig('carriers/premiumrate/calculate_greater_volume');
		$this->_alwaysUseWeight = Mage::getStoreConfig('carriers/premiumrate/always_weight');
		$addOr = FALSE;

		$this->_request = $request;

    	if ($request->getPRConditionName()=='package_volweight')
		{
			$greaterVolume=true;
			$totalVolweight = $this->getVolumeWeight($request);
			if ($usingGreaterVolLogic && $request->getData('package_weight')> $totalVolweight) {
				$greaterVolume=false;
				$totalVolweight=$request->getData('package_weight');
			}
		}

 		$zipcodeMaxLength = Mage::getStoreConfig('carriers/premiumrate/zipcode_max_length');
    	if(empty($zipcodeMaxLength))
    	{
		   if ( Mage_Usa_Model_Shipping_Carrier_Abstract::USA_COUNTRY_ID == $request->getDestCountryId())
		   {
		   	$splitPostcode = explode('-',$request->getDestPostcode());
		   	$postcode = $splitPostcode[0];
		   }
		   else if ( 'BR' == $request->getDestCountryId())
		   {
		   	$postcode = str_replace("-","",$request->getDestPostcode());
		   }
		   else if ( 'NL' == $request->getDestCountryId())
		   {
		       $postcode = substr($request->getDestPostcode(), 0, 4);
		   }
		   else
		   {
		    $postcode = $request->getDestPostcode();
		   }
    	 }
    	else
    	 {
    	  $postcode = substr($request->getDestPostcode(), 0, $zipcodeMaxLength);
		 }

		Mage::Helper('premiumrate')->processZipcode($read, $postcode,
			$this->_twoPhaseFiltering, $this->_zipSearchString, $this->_shortMatchPostcode, $this->_longMatchPostcode );


		if ($this->_twoPhaseFiltering) {
			$switchSearches=13;
		} else {
			$switchSearches=9;
		}
    	for ($j=0;$j<$switchSearches;$j++)
		{
			$select = $this->getSwitchSelect($read,$j);

			if ($request->getPRConditionName()=='package_volweight') {
				if ($usingGreaterVolLogic) {
					$select->where('condition_name=?', $request->getPRConditionName());
					$select->where('weight_from_value<=?', $totalVolweight);
					$select->where('weight_to_value>=?', $totalVolweight);
					$select->where('price_from_value<=?', $request->getData('package_value'));
					$select->where('price_to_value>=?', $request->getData('package_value'));
					$select->where('item_from_value<=?', $request->getData('package_qty'));
					$select->where('item_to_value>=?', $request->getData('package_qty'));
					if (!empty($this->_alwaysUseWeight)){
						foreach (explode(",",$this->_alwaysUseWeight) as $method) {
							$select->where('delivery_type<>?', $method);
						}
						$addOr = TRUE;
					}
				} else {
					$select->where('condition_name=?', $request->getPRConditionName());
					$select->where('weight_from_value<=?', $request->getData('package_weight'));
					$select->where('weight_to_value>=?', $request->getData('package_weight'));
					$select->where('price_from_value<=?', $request->getData('package_value'));
					$select->where('price_to_value>=?', $request->getData('package_value'));
					$select->where('item_from_value<=?', $totalVolweight);
					$select->where('item_to_value>=?', $totalVolweight);
				}
			} else {
				$select->where('condition_name=?', $request->getPRConditionName());
				$select->where('weight_from_value<=?', $request->getData('package_weight'));
				$select->where('weight_to_value>=?', $request->getData('package_weight'));
				$select->where('price_from_value<=?', $request->getData('package_value'));
				$select->where('price_to_value>=?', $request->getData('package_value'));
				$select->where('item_from_value<=?', $request->getData('package_qty'));
				$select->where('item_to_value>=?', $request->getData('package_qty'));
			}

			$select->where('website_id=?', $request->getWebsiteId());

			$select->order('sort_order ASC');

			if($addOr) {
				$actualWeightSelect = $this->getSwitchSelect($read,$j);
				$actualWeightSelect->where('condition_name=?', $request->getPRConditionName());
				$actualWeightSelect->where('weight_from_value<=?', $request->getData('package_weight'));
				$actualWeightSelect->where('weight_to_value>=?', $request->getData('package_weight'));
				$actualWeightSelect->where('price_from_value<=?', $request->getData('package_value'));
				$actualWeightSelect->where('price_to_value>=?', $request->getData('package_value'));
				$actualWeightSelect->where('item_from_value<=?', $request->getData('package_qty'));
				$actualWeightSelect->where('item_to_value>=?', $request->getData('package_qty'));

				$methods = explode(",",$this->_alwaysUseWeight);
				$actualWeightSelect->where('delivery_type IN (?)',$methods);

				$actualWeightSelect->where('website_id=?', $request->getWebsiteId());
				$actualWeightSelect->order('sort_order ASC');
			}
			/*
			pdo has an issue. we cannot use bind
			*/
			$newdata=array();
			try {
				$row = $read->fetchAll($select);
				if($addOr) {
					$row2 = $read->fetchAll($actualWeightSelect);
				}
			} catch (Exception $e) {
				 	Mage::helper('wsalogger/log')->postCritical('premiumrate','SQL Exception',$e,$this->_debug);
			}

			if($addOr) {
				$row = array_merge($row,$row2);
			}

			if (!empty($row))
			{
				if ($this->_debug) {
					Mage::helper('wsalogger/log')->postInfo('premiumrate','SQL Select',$select->getPart('where'));
					if($addOr) {
						Mage::helper('wsalogger/log')->postInfo('premiumrate','SQL Select for Actual Weight Only Methods',$actualWeightSelect->getPart('where'));
					}
					Mage::helper('wsalogger/log')->postInfo('premiumrate','SQL Result',$row);
				}
				// have found a result or found nothing and at end of list!
				foreach ($row as $data) {
					if ($data['price']==-1) {
					    $this->_excludedDeliveries[]=$data['delivery_type'];
						continue;
					}
					$data['method_name']=$data['delivery_type'];
					if ($data['algorithm']!="") {
						$algorithm_array=explode("&",$data['algorithm']);  // Multi-formula extension
						reset($algorithm_array);
						$skipData=false;
						foreach ($algorithm_array as $algorithm_single) {
							$algorithm=explode("=",$algorithm_single,2);
							if (!empty($algorithm) && count($algorithm)==2) {
								if (strtolower($algorithm[0])=="w") {
									// weight based
									$weightIncrease=explode("@",$algorithm[1]);
									if (!empty($weightIncrease) && count($weightIncrease)==2 ) {
										if ($usingGreaterVolLogic && $request->getPRConditionName()=='package_volweight' && $greaterVolume ) {
											$weightDifference =	$totalVolweight-$data['weight_from_value'];
										} else {
											$weightDifference =	$request->getData('package_weight')-$data['weight_from_value'];
										}
										$quotient=ceil($weightDifference / $weightIncrease[0]);
										$data['price']=$data['price']+$weightIncrease[1]*$quotient;
									}
                                } else if (strtolower($algorithm[0])=="wnc") {
                                    // weight based without rounding
									$weightIncrease=explode("@",$algorithm[1]);
									if (!empty($weightIncrease) && count($weightIncrease)==2 ) {
										if ($usingGreaterVolLogic && $request->getPRConditionName()=='package_volweight' && $greaterVolume ) {
											$weightDifference =	$totalVolweight-$data['weight_from_value'];
										} else {
											$weightDifference =	$request->getData('package_weight')-$data['weight_from_value'];
										}
										$quotient=$weightDifference / $weightIncrease[0];
										$data['price']=$data['price']+$weightIncrease[1]*$quotient;
									}
								} else if (strtolower($algorithm[0])=="aw") {
									// always weight based
									$weightIncrease=explode("@",$algorithm[1]);
									if (!empty($weightIncrease) && count($weightIncrease)==2 ) {
										$weightDifference =	$request->getData('package_weight')-$data['weight_from_value'];
										$quotient=ceil($weightDifference / $weightIncrease[0]);
										$data['price']=$data['price']+$weightIncrease[1]*$quotient;
									}
								} else if (strtolower($algorithm[0])=="pr") {
                                    //  price based
                                    $priceIncrease=explode("@",$algorithm[1]);
                                    if (!empty($priceIncrease) && count($priceIncrease)==2 ) {
                                        $priceDifference =	$request->getData('package_value')-$data['price_from_value'];
                                        $quotient=ceil($priceDifference / $priceIncrease[0]);
                                        $data['price']=$data['price']+$priceIncrease[1]*$quotient;
                                    }
                                } else if (strtolower($algorithm[0])=="v") {
									// volume based
									$weightIncrease=explode("@",$algorithm[1]);
									if (!empty($weightIncrease) && count($weightIncrease)==2 ) {
										$weightDifference=	$totalVolweight-$data['item_from_value'];
										$quotient=ceil($weightDifference / $weightIncrease[0]);
										$data['price']=$data['price']+$weightIncrease[1]*$quotient;
									}
								} else if (strtolower($algorithm[0])=="i") {
									// volume based
									$perItemCost=$algorithm[1];
									if (!empty($perItemCost)) {
										$numItemsAffected =	$request->getData('package_qty')-$data['item_from_value'];
										$data['price']=$data['price']+$perItemCost*$numItemsAffected;
									}
								} else if (strtolower($algorithm[0])=="ai") {
									//all items
									$itemCost=$algorithm[1];
									if (!empty($itemCost)) {
										$data['price'] = $data['price']+$itemCost*$request->getData('package_qty');
									}
								} else if (strtolower($algorithm[0])=="instock" && strtolower($algorithm[1])=="true") {
									// in stock
									if (!$this->_stockFound) {
										if ($this->checkOutOfStock($request)) {
											$skipData = true;
											break;
										}
									} else {
										if ($this->_outofstock) {
											$skipData = true;
											break;
										}
									}
								} else if (strtolower($algorithm[0])=="instock" && strtolower($algorithm[1])=="false") {
									if (!$this->_stockFound) {
										if (!$this->checkOutOfStock($this->_request)) {
											$skipData = true;
											break;
										}
									} else {
										if (!$this->_outofstock) {
											$skipData = true;
											break;
										}
									}
								} else if (strtolower($algorithm[0])=="m") {
									$data['method_name']=$algorithm[1];
								}
							}
						}
						if ($skipData) {
							continue;
						}
					}
					$newData[]=$data;
				}
				break;
			} else {
				if ($this->_debug) {
					Mage::helper('wsalogger/log')->postDebug('premiumrate','SQL Select',$select->getPart('where'));
				}
			}
		}

		if(empty($newData)){return;};

		if (!empty($this->_excludedDeliveries)) {
		    foreach ($newData as $key=>$result) {
		        foreach ($this->_excludedDeliveries as $ekey=>$exclusionItem) {
		            if ($result['delivery_type']==$exclusionItem) {
		                $newData[$key]="";
		                break;
		            }
		        }

		    }
		    foreach ($newData as $key=>$result) {
		        if (empty($newData[$key])) {
		            unset($newData[$key]);
		        }
		    }
		}

		if(!empty($newData)){ return $newData;} else return;
    }

	private function getSwitchSelect($read,$j)
	{
    	$select = $read->select()->from($this->_table);

    	if($this->_twoPhaseFiltering) {
    		switch($j) {
    			case 0:
    				$select->where(
    				$read->quoteInto(" (dest_country_id=? ",$this->_request->getDestCountryId()).
    				$read->quoteInto(" AND dest_region_id=? ", $this->_request->getDestRegionId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_city),LOWER(?)) = 0  ", $this->_request->getDestCity()).

    				$read->quoteInto(" AND STRCMP(LOWER(dest_zip),LOWER(?)) = 0)", $this->_longMatchPostcode)
    				);
    				break;
    			case 1:
    				$select->where(
    				$read->quoteInto(" (dest_country_id=? ",$this->_request->getDestCountryId()).
    				$read->quoteInto(" AND dest_region_id=? ", $this->_request->getDestRegionId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_city),LOWER(?)) = 0  ", $this->_request->getDestCity()).
    				$this->_shortMatchPostcode
    				);
    				break;
    			case 2:
    				$select->where(
    				$read->quoteInto(" (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND dest_region_id=?  AND dest_city=''", $this->_request->getDestRegionId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_zip),LOWER(?)) = 0)", $this->_longMatchPostcode)
    				);
    				break;
    			case 3:
    				$select->where(
    				$read->quoteInto(" (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND dest_region_id=?  AND dest_city=''", $this->_request->getDestRegionId()).
    				$this->_shortMatchPostcode
    				);
    				break;
    			case 4:
    				$select->where(
    				$read->quoteInto(" (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND dest_region_id=? ", $this->_request->getDestRegionId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_city),LOWER(?)) = 0  AND dest_zip='')", $this->_request->getDestCity())
    				);
    				break;
    			case 5:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_city),LOWER(?)) = 0  AND dest_region_id='0'", $this->_request->getDestCity()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_zip),LOWER(?)) = 0)", $this->_longMatchPostcode)
    				);
    				break;
    			case 6:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_city),LOWER(?)) = 0  AND dest_region_id='0'", $this->_request->getDestCity()).
    				$this->_shortMatchPostcode
    				);
    				break;
    			case 7:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_city),LOWER(?)) = 0  AND dest_region_id='0' AND dest_zip='') ", $this->_request->getDestCity())
    				);
    				break;
    			case 8:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? AND dest_region_id='0' AND dest_city='' ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_zip),LOWER(?)) = 0)", $this->_longMatchPostcode)
    				);
    				break;
    			case 9:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? AND dest_region_id='0' AND dest_city='' ", $this->_request->getDestCountryId()).
    				$this->_shortMatchPostcode
    				);
    				break;
    			case 10:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND dest_region_id=? AND dest_city='' AND dest_zip='') ", $this->_request->getDestRegionId())
    				);
    				break;

    			case 11:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? AND dest_region_id='0' AND dest_city='' AND dest_zip='') ", $this->_request->getDestCountryId())
    				);
    				break;

    			case 12:
    				$select->where("  (dest_country_id='0' AND dest_region_id='0' AND dest_zip='')" );
    				break;
    		}
    	}
    	else {
    		switch($j) {
    			case 0:
    				$select->where(
    				$read->quoteInto(" (dest_country_id=? ",$this->_request->getDestCountryId()).
    				$read->quoteInto(" AND dest_region_id=? ", $this->_request->getDestRegionId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_city),LOWER(?)) = 0  ", $this->_request->getDestCity()).
    				$this->_zipSearchString
    				);
    				break;
    			case 1:
    				$select->where(
    				$read->quoteInto(" (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND dest_region_id=?  AND dest_city=''", $this->_request->getDestRegionId()).
    				$this->_zipSearchString
    				);
    				break;
    			case 2:
    				$select->where(
    				$read->quoteInto(" (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND dest_region_id=? ", $this->_request->getDestRegionId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_city),LOWER(?)) = 0  AND dest_zip='')", $this->_request->getDestCity())
    				);
    				break;
    			case 3:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_city),LOWER(?)) = 0  AND dest_region_id='0'", $this->_request->getDestCity()).
    				$this->_zipSearchString
    				);
    				break;
    			case 4:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND STRCMP(LOWER(dest_city),LOWER(?)) = 0  AND dest_region_id='0' AND dest_zip='') ", $this->_request->getDestCity())
    				);
    				break;
    			case 5:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? AND dest_region_id='0' AND dest_city='' ", $this->_request->getDestCountryId()).
    				$this->_zipSearchString
    				    				);
    				break;
    			case 6:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? ", $this->_request->getDestCountryId()).
    				$read->quoteInto(" AND dest_region_id=? AND dest_city='' AND dest_zip='') ", $this->_request->getDestRegionId())
    				);
    				break;

    			case 7:
    				$select->where(
    				$read->quoteInto("  (dest_country_id=? AND dest_region_id='0' AND dest_city='' AND dest_zip='') ", $this->_request->getDestCountryId())
    				);
    				break;

    			case 8:
    				$select->where("  (dest_country_id='0' AND dest_region_id='0' AND dest_zip='')" );
    				break;
    		}
    	}
    	return $select;
    }

    private function checkOutOfStock($request) {
		$items = $request->getAllItems();
    	foreach($items as $item) {
    		if ($item->getBackorders() != Mage_CatalogInventory_Model_Stock::BACKORDERS_NO) {
    			$this->_outofstock=true;
    		}
    	}
    	$this->_stockFound=true;
    	return $this->_outofstock;
    }

    private function getVolumeWeight($request) {
		$total_volweight=0;
		$configurableQty = 0;
		$items = $request->getAllItems();
		foreach($items as $item) {
			$currentQty = $item->getQty();
			if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
				$configurableQty = $currentQty;
				continue;
			} elseif ($configurableQty > 0) {
				$currentQty = $configurableQty;
				$configurableQty = 0;
			}
			$parentQty = 1;
			if ($item->getParentItem()!=null) {
				if ($item->getParentItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
					$parentQty = $item->getParentItem()->getQty();
				}
			}
			$qty=$currentQty * $parentQty;

			$product=Mage::getModel('catalog/product')->load( $item->getProductId() );
			$total_volweight  += ($product->getVolumeWeight()*$qty);

		}
		return $total_volweight;
    }





    public function uploadAndImport(Varien_Object $object)
    {
        $csvFile = $_FILES["groups"]["tmp_name"]["premiumrate"]["fields"]["import"]["value"];
        $csvName = $_FILES["groups"]["name"]["premiumrate"]["fields"]["import"]["value"];
		$session = Mage::getSingleton('adminhtml/session');
        $dataStored = false;
		
        if (!empty($csvFile)) {
			
            $csv = trim(file_get_contents($csvFile));

            $table = Mage::getSingleton('core/resource')->getTableName('premiumrate_shipping/premiumrate');
			
			$websiteId = $object->getScopeId();
            $websiteModel = Mage::app()->getWebsite($websiteId);

            Mage::helper('wsacommon/shipping')->saveCSV($csv, $csvName, $websiteId, 'premiumrate');
           
            /*
            getting condition name from post instead of the following commented logic
            */

            if (isset($_POST['groups']['premiumrate']['fields']['condition_name']['inherit'])) {
                $conditionName = (string)Mage::getConfig()->getNode('default/carriers/premiumrate/condition_name');
            } else {
                $conditionName = $_POST['groups']['premiumrate']['fields']['condition_name']['value'];
            }

            $conditionFullName = Mage::getModel('premiumrate_shipping/carrier_premiumrate')->getCode('condition_name_short', $conditionName);
            if (!empty($csv)) {
                $exceptions = array();
                $csvLines = explode("\n", $csv);
                $csvLine = array_shift($csvLines);
                $csvLine = $this->_getCsvValues($csvLine);
                if (count($csvLine) < 15) {
                    $exceptions[0] = Mage::helper('shipping')->__('Invalid Premium Matrix Rates File Format');
                }

                $countryCodes = array();
                $regionCodes = array();
                foreach ($csvLines as $k=>$csvLine) {
                    $csvLine = $this->_getCsvValues($csvLine);
                    if (count($csvLine) > 0 && count($csvLine) < 15) {
                        $exceptions[0] = Mage::helper('shipping')->__('Invalid Premium Matrix Rates File Format');
                    } else {
                    	$splitCountries = explode(",", trim($csvLine[0]));
                    	$splitRegions = explode(",", trim($csvLine[1]));
                    	foreach ($splitCountries as $country) {
                    		if (!in_array($country,$countryCodes)) {
                        		$countryCodes[] = trim($country);
                    		}
                    	}
                       foreach ($splitRegions as $region) {
	                        	$regionCodes[] = $region;
	                    	}
	                   	}
                    }
                }

                if (empty($exceptions)) {
                    $connection = $this->_getWriteAdapter();

                     $condition = array(
                        $connection->quoteInto('website_id = ?', $websiteId),
                    	$connection->quoteInto('condition_name = ?', $conditionName),
                    );
                    $connection->delete($table, $condition);


                }
                if (!empty($exceptions)) {
                    throw new Exception( "\n" . implode("\n", $exceptions) );
                }



                if (empty($exceptions)) {
                    $data = array();
                    $countryCodesToIds = array();
                    $regionCodesToIds = array();
                    $countryCodesIso2 = array();
                    $counter = 0;
                    $countryCollection = Mage::getResourceModel('directory/country_collection')->addCountryCodeFilter($countryCodes)->load();
                    foreach ($countryCollection->getItems() as $country) {
                        $countryCodesToIds[$country->getData('iso3_code')] = $country->getData('country_id');
                        $countryCodesToIds[$country->getData('iso2_code')] = $country->getData('country_id');
                        $countryCodesIso2[] = $country->getData('iso2_code');
                    }

     				$regionCollection = Mage::getResourceModel('directory/region_collection')
                        ->addRegionCodeFilter($regionCodes)
                        ->addCountryFilter($countryCodesIso2)
                        ->load();

                    foreach ($regionCollection->getItems() as $region) {
                        $regionCodesToIds[$countryCodesToIds[$region->getData('country_id')]][$region->getData('code')] = $region->getData('region_id');
                    }

                    foreach ($csvLines as $k=>$csvLine) {

                        $csvLine = $this->_getCsvValues($csvLine);
                        $splitCountries = explode(",", trim($csvLine[0]));
                        $splitRegions = explode(",", trim($csvLine[1]));
                        //$splitPostcodes = explode(",",trim($csvLine[3]));
                        $splitPostcodes = explode(",",strtoupper(trim($csvLine[3])));

						if ($csvLine[2] == '*' || $csvLine[2] == '') {
							$city = '';
						} else {
							$city = $csvLine[2];
						}


						if ($csvLine[4] == '*' || $csvLine[4] == '') {
							$zip_to = '';
						} else {
							$zip_to = strtoupper(trim($csvLine[4]));
						}



						if ( $csvLine[5] == '*' || $csvLine[5] == '') {
							$weightFrom = 0;
						} else if (!$this->_isPositiveDecimalNumber($csvLine[5]) ) {
							$exceptions[] = Mage::helper('shipping')->__('Invalid Weight From "%s" in the Row #%s',  $csvLine[5], ($k+1));
						} else {
							$weightFrom = (float)$csvLine[5];
						}


						if ( $csvLine[6] == '*' || $csvLine[6] == '') {
							$weightTo = 10000000;
						} else if (!$this->_isPositiveDecimalNumber($csvLine[6]) ) {
							$exceptions[] = Mage::helper('shipping')->__('Invalid Weight To "%s" in the Row #%s',  $csvLine[6], ($k+1));
						} else {
							$weightTo = (float)$csvLine[6];
						}

						if ( $csvLine[7] == '*' || $csvLine[7] == '') {
							$priceFrom = 0;
						} else if (!$this->_isPositiveDecimalNumber($csvLine[7]) ) {
							$exceptions[] = Mage::helper('shipping')->__('Invalid Price From "%s" in the Row #%s',  $csvLine[7], ($k+1));
						} else {
							$priceFrom = (float)$csvLine[7];
						}


						if ( $csvLine[8] == '*' || $csvLine[8] == '') {
							$priceTo = 10000000;
						} else if (!$this->_isPositiveDecimalNumber($csvLine[8]) ) {
							$exceptions[] = Mage::helper('shipping')->__('Invalid Price To "%s" in the Row #%s',  $csvLine[8], ($k+1));
						} else {
							$priceTo = (float)$csvLine[8];
						}

                        if ( $csvLine[9] == '*' || $csvLine[9] == '') {
							$itemFrom = 0;
						} else {
							$itemFrom = $csvLine[9];
						}


						if ( $csvLine[10] == '*' || $csvLine[10] == '') {
							$itemTo = 10000000;
						} else {
							$itemTo = $csvLine[10];
						}

						if ( $csvLine[12] == '*' || $csvLine[12] == '') {
							$algorithm = '';
						} else {
							$algorithm=$csvLine[12];
						}
                        if ( $csvLine[14] == '*' || $csvLine[14] == '') {
							$sortOrder = 0;
						} else {
							$sortOrder=$csvLine[14];
						}
                        foreach ($splitCountries as $country) {

                        	$country = trim($country);
	                        if (empty($countryCodesToIds) || !array_key_exists($country, $countryCodesToIds)) {
	                            $countryId = '0';
	                            if ($country != '*' && $country != '') {
	                                $exceptions[] = Mage::helper('shipping')->__('Invalid Country "%s" in the Row #%s',$country, ($k+1));
	                            }
	                        } else {
	                            $countryId = $countryCodesToIds[$country];
	                        }
                        	foreach ($splitRegions as $region) {

                        		if (!isset($countryCodesToIds[$country])
		                            || !isset($regionCodesToIds[$countryCodesToIds[$country]])
		                            || !array_key_exists($region, $regionCodesToIds[$countryCodesToIds[$country]])) {
		                            $regionId = '0';
			                        if ($region != '*' && $region != '') {
		                            	$exceptions[] = Mage::helper('shipping')->__('Invalid Region/State "%s" in the Row #%s', $region, ($k+1));
		                            }
		                        } else {
		                            $regionId = $regionCodesToIds[$countryCodesToIds[$country]][$region];
		                        }

                        		foreach ($splitPostcodes as $postcode){

										if ($postcode == '*' || $postcode == '') {
											$zip = '';
											$new_zip_to = '';
										} else {
											$zip_str = explode("-", $postcode);
											if(count($zip_str) != 2)
											{
												$zip = trim($postcode);
												if (ctype_digit($postcode) && trim($zip_to) == '') {
													$new_zip_to = trim($postcode);
												} else $new_zip_to = $zip_to;
											}
											else {
												$zip = trim($zip_str[0]);
												$new_zip_to = trim($zip_str[1]);
											}
										}

									$data[] = array('website_id'=>$websiteId, 'dest_country_id'=>$countryId, 'dest_region_id'=>$regionId,
										'dest_city'=>$city, 'dest_zip'=>$zip, 'dest_zip_to'=>$new_zip_to, 'condition_name'=>$conditionName,
										'weight_from_value'=>$weightFrom,'weight_to_value'=>$weightTo,
										'price_from_value'=>$priceFrom,'price_to_value'=>$priceTo,
										'item_from_value'=>$itemFrom,'item_to_value'=>$itemTo,
										'price'=>$csvLine[11], 'algorithm'=>$algorithm, 'delivery_type'=>$csvLine[13], 'sort_order'=>$sortOrder);


									$dataDetails[] = array('country'=>$country, 'region'=>$region);
									$counter++;
	                        	}
							}
		                	$dataStored = false;
		                   	if (!empty($exceptions)) {
				            	break;
				            }
							if($counter>1000) {
			                    foreach($data as $k=>$dataLine) {
			                        try {
			                           $connection->insert($table, $dataLine);
			                        } catch (Exception $e) {
			                                   	$messageStr = Mage::helper('shipping')->__('Error# 302 - Duplicate Row #%s (Country "%s", Region/State "%s", Zip "%s")',
			                                   	($k+1), $dataDetails[$k]['country'], $dataDetails[$k]['region'], $dataLine['dest_zip']);


	            								$exceptions[] = $messageStr;
			                                   	Mage::helper('wsalogger/log')->postWarning('premiumrate','Duplicate Row',$messageStr,$this->_debug,
	            									'302','http://wiki.webshopapps.com/troubleshooting-guide/duplicate-row-error');

			                     		//$exceptions[] = Mage::helper('shipping')->__($e);
			                        }
			                    }
	            				Mage::helper('wsacommon/shipping')->updateStatus($session,count($data));
			                    $counter = 0;
			                    unset($data);
			                    unset($dataDetails);
			                    $dataStored = true;
							}
                        }
                  }

	            if(empty($exceptions) && !$dataStored) {
	            	foreach($data as $k=>$dataLine) {
	            		try {
	            			$connection->insert($table, $dataLine);
	            		} catch (Exception $e) {
	            			 $messageStr = Mage::helper('shipping')->__('Error# 302 - Duplicate Row #%s (Country "%s", Region/State "%s", Zip "%s")',
	            			($k+1), $dataDetails[$k]['country'], $dataDetails[$k]['region'], $dataLine['dest_zip']);
	            			$exceptions[] = $messageStr;

	            			Mage::helper('wsalogger/log')->postWarning('premiumrate','Duplicate Row',$messageStr,$this->_debug,
	            				302,'http://wiki.webshopapps.com/troubleshooting-guide/duplicate-row-error');

	            		}
	            	}
	            	Mage::helper('wsacommon/shipping')->updateStatus($session,count($data));

	            }
	            if (!empty($exceptions)) {
	            	throw new Exception( "\n" . implode("\n", $exceptions) );
	            }
        	}
        }
	}

    private function _getCsvValues($string, $separator=",")
    {
        $elements = explode($separator, trim($string));
        for ($i = 0; $i < count($elements); $i++) {
            $nquotes = substr_count($elements[$i], '"');
            if ($nquotes %2 == 1) {
                for ($j = $i+1; $j < count($elements); $j++) {
                    if (substr_count($elements[$j], '"') > 0) {
                        // Put the quoted string's pieces back together again
                        array_splice($elements, $i, $j-$i+1, implode($separator, array_slice($elements, $i, $j-$i+1)));
                        break;
                    }
                }
            }
            if ($nquotes > 0) {
                // Remove first and last quotes, then merge pairs of quotes
                $qstr =& $elements[$i];
                $qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
                $qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
                $qstr = str_replace('""', '"', $qstr);
            }
            $elements[$i] = trim($elements[$i]);
        }
        return $elements;
    }

    private function _isPositiveDecimalNumber($n)
    {
        return preg_match ("/^[0-9]+(\.[0-9]*)?$/", $n);
    }

}
