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
 * @package    Mage_Shipping
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

class Webshopapps_Wsacommon_Model_Shipping_Shipping extends Mage_Shipping_Model_Shipping
{


    /**
     * Prefix of model events
     *
     * @var string
     */
    protected static $_eventPrefix = 'webshopapps_shipping_shipping';

    /**
     * Name of event object
     *
     * @var string
     */
    protected static $_eventObject = 'shipping';

	/**
     * Retrieve all methods for supplied shipping data
     *
     * @todo make it ordered
     * @param Mage_Shipping_Model_Shipping_Method_Request $data
     * @return Mage_Shipping_Model_Shipping
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {

        Mage::dispatchEvent(self::$_eventPrefix . '_collectRates_before', array(self::$_eventObject => $this));


        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Dropship','carriers/dropship/active')) {
     		if (!Mage::registry('dropship_shipmodel')) {
				$model = Mage::getModel('dropship/shipping_shipping');
				Mage::register('dropship_shipmodel', $model);
			}
			Mage::registry('dropship_shipmodel')->resetResult();
			return Mage::registry('dropship_shipmodel')->collectRates($request);
	 	}


	 	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Rgshipping','shipping/rgshipping/active')) {
     		if (!Mage::registry('rgshipping_shipmodel')) {
				$model = Mage::getModel('rgshipping/shipping_shipping');
				Mage::register('rgshipping_shipmodel', $model);
			}
			Mage::registry('rgshipping_shipmodel')->resetResult();
			return Mage::registry('rgshipping_shipmodel')->collectRates($request);
	 	}

    	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Shipmanager','carriers/shipmanager/active')) {
     		if (!Mage::registry('shipmanager_shipmodel')) {
				$model = Mage::getModel('shipmanager/shipping_shipping');
				Mage::register('shipmanager_shipmodel', $model);
			}
			Mage::registry('shipmanager_shipmodel')->resetResult();
			return Mage::registry('shipmanager_shipmodel')->collectRates($request);
	 	}

	 	
	 	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Shippingoverride2','shipping/shippingoverride2/active')) {
	 		if (!Mage::registry('override2_shipmodel')) {
	 			$model = Mage::getModel('shippingoverride2/shipping_shipping');
	 			Mage::register('override2_shipmodel', $model);
	 		}
	 		Mage::registry('override2_shipmodel')->resetResult();
	 		return Mage::registry('override2_shipmodel')->collectRates($request);
	 	}

    	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Freightrate','carriers/freightrate/active')) {
     		if (!Mage::registry('freightrate_shipmodel')) {
				$model = Mage::getModel('freightrate/shipping_shipping');
				Mage::register('freightrate_shipmodel', $model);
			}
			Mage::registry('freightrate_shipmodel')->resetResult();
			return Mage::registry('freightrate_shipmodel')->collectRates($request);
	 	}

	 	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsafreightcommon','shipping/wsafreightcommon/active')) {
	 		if (!Mage::registry('wsafreightcommon_shipmodel')) {
	 			$model = Mage::getModel('wsafreightcommon/shipping_shipping');
	 			Mage::register('wsafreightcommon_shipmodel', $model);
	 		}
	 		Mage::registry('wsafreightcommon_shipmodel')->resetResult();
	 		return Mage::registry('wsafreightcommon_shipmodel')->collectRates($request);
	 	}

     	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Estesfreight','carriers/estesfreight/active')) {
     		if (!Mage::registry('estes_shipmodel')) {
				$model = Mage::getModel('estesfreight/shipping_shipping');
				Mage::register('estes_shipmodel', $model);
			}
			Mage::registry('estes_shipmodel')->resetResult();
			return Mage::registry('estes_shipmodel')->collectRates($request);
	 	}

     	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Conwayfreight','carriers/conwayfreight/active')) {
     		if (!Mage::registry('conway_shipmodel')) {
				$model = Mage::getModel('conwayfreight/shipping_shipping');
				Mage::register('conway_shipmodel', $model);
			}
			Mage::registry('conway_shipmodel')->resetResult();
			return Mage::registry('conway_shipmodel')->collectRates($request);
	 	}

    	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsafedexfreight','carriers/wsafedexfreight/active')) {
     		if (!Mage::registry('wsafedexfreight_shipmodel')) {
				$model = Mage::getModel('wsafedexfreight/shipping_shipping');
				Mage::register('wsafedexfreight_shipmodel', $model);
			}
			Mage::registry('wsafedexfreight_shipmodel')->resetResult();
			return Mage::registry('wsafedexfreight_shipmodel')->collectRates($request);
	 	}

    	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Usslogistics','carriers/usslogistics/active')) {
     		if (!Mage::registry('usslogistics_shipmodel')) {
				$model = Mage::getModel('usslogistics/shipping_shipping');
				Mage::register('usslogistics_shipmodel', $model);
			}
			Mage::registry('usslogistics_shipmodel')->resetResult();
			return Mage::registry('usslogistics_shipmodel')->collectRates($request);
	 	}


     	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Abffreight','carriers/abffreight/active')) {
     		if (!Mage::registry('abffreight_shipmodel')) {
				$model = Mage::getModel('abffreight/shipping_shipping');
				Mage::register('abffreight_shipmodel', $model);
			}
			Mage::registry('abffreight_shipmodel')->resetResult();
			return Mage::registry('abffreight_shipmodel')->collectRates($request);
	 	}

        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsaupsfreight','carriers/wsaupsfreight/active')) {
     		if (!Mage::registry('wsaupfreight_shipmodel')) {
				$model = Mage::getModel('wsaupsfreight/shipping_shipping');
				Mage::register('wsaupfreight_shipmodel', $model);
			}
			Mage::registry('wsaupfreight_shipmodel')->resetResult();
			return Mage::registry('wsaupfreight_shipmodel')->collectRates($request);
	 	}

     	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Yrcfreight','carriers/yrcfreight/active')) {
     		if (!Mage::registry('yrc_shipmodel')) {
				$model = Mage::getModel('yrcfreight/shipping_shipping');
				Mage::register('yrc_shipmodel', $model);
			}
			Mage::registry('yrc_shipmodel')->resetResult();
			return Mage::registry('yrc_shipmodel')->collectRates($request);
	 	}

    	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Rlfreight','carriers/rlfreight/active')) {
     		if (!Mage::registry('rlfreight_shipmodel')) {
				$model = Mage::getModel('rlfreight/shipping_shipping');
				Mage::register('rlfreight_shipmodel', $model);
			}
			Mage::registry('rlfreight_shipmodel')->resetResult();
			return Mage::registry('rlfreight_shipmodel')->collectRates($request);
	 	}

	 	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Handlingproduct','shipping/handlingproduct/active')) {
			if (!Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Shipusa','shipping/shipusa/active')) {
	     		if (Mage::registry('handlingproduct_shipmodel')) {
					Mage::registry('handlingproduct_shipmodel')->resetResult();

				}
			}
	 	}
        // This method of handling rewrites is now deprecated in favour of using event logic for handling matrix
        // Only remains in case Handling Matrix code has not been updated
	 	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Handlingmatrix','shipping/handlingmatrix/active') &&
            Mage::helper('wsacommon')->getNumericExtensionVersion('handlingmatrix')<100) {
			if (Mage::registry('handlingmatrix_shipmodel')) {
					Mage::registry('handlingmatrix_shipmodel')->resetResult();
			}
	 	}

	 	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Insurance','shipping/insurance/active')) {
     		if (Mage::registry('insurance_shipmodel')) {
					Mage::registry('insurance_shipmodel')->resetResult();
     		}
	 	}

        parent::collectRates($request);

        Mage::dispatchEvent(self::$_eventPrefix . '_collectRates_after', array(self::$_eventObject => $this,
                'request' => $request));

        return $this;
    }


	/**
	 * Overrides this method in core, and decides which extension to call
	 * Uses a hierarchy to decide on best extension
	 * @see app/code/core/Mage/Shipping/Model/Mage_Shipping_Model_Shipping::collectCarrierRates()
	 */
 	public function collectCarrierRates($carrierCode, $request)
 	{

 		// check to see if handling Product enabled
	 	if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Handlingproduct','shipping/handlingproduct/active')) {
			if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Shipusa','shipping/shipusa/active')) {
		 		return parent::collectCarrierRates($carrierCode,$request);

		 	} else {
		 		if (!Mage::registry('handlingproduct_shipmodel')) {
					$model = Mage::getModel('handlingproduct/shipping_shipping');
					Mage::register('handlingproduct_shipmodel', $model);
				}
				$model = Mage::registry('handlingproduct_shipmodel') ;
				$model->collectCarrierRates($carrierCode, $request);
				$this->_result=$model->getResult();
				return $model;

		 	}
		}

        // This method of handling rewrites is now deprecated in favour of using event logic for handling matrix
        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Handlingmatrix','shipping/handlingmatrix/active') &&
            Mage::helper('wsacommon')->getNumericExtensionVersion('handlingmatrix')<100) {
			if (!Mage::registry('handlingmatrix_shipmodel')) {
				$model = Mage::getModel('handlingmatrix/shipping_shipping');
				Mage::register('handlingmatrix_shipmodel', $model);
			}
			$model = Mage::registry('handlingmatrix_shipmodel');
			$model->collectCarrierRates($carrierCode, $request);
			$this->_result=$model->getResult();
			return $model;
		}

        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Insurance','shipping/insurance/active')) {
     		if (!Mage::registry('insurance_shipmodel')) {
				$model = Mage::getModel('insurance/shipping_shipping');
				Mage::register('insurance_shipmodel', $model);
			}
			$model = Mage::registry('insurance_shipmodel');
			$model->collectCarrierRates($carrierCode, $request);
			$this->_result=$model->getResult();
			return $model;
         }


	 	// default
	 	return parent::collectCarrierRates($carrierCode,$request);

	 }


}