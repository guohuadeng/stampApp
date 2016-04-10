<?php
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Wsacommon
 * User         karen
 * Date         26/08/2013
 * Time         14:42
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Wsacommon_Model_Sales_Quote_Address_Rate extends Mage_Sales_Model_Quote_Address_Rate
{


    public function importShippingRate(Mage_Shipping_Model_Rate_Result_Abstract $rate)
    {

        if (!Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Dropship','carriers/dropship/active') &&
          !Mage::helper('wsacommon')->isModuleEnabled(
              'Webshopapps_Wsafreightcommon','shipping/wsafreightcommon/active') ) {
            return parent::importShippingRate($rate);
        }

        if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error) {
            $this
                ->setCode($rate->getCarrier().'_error')
                ->setCarrier($rate->getCarrier())
                ->setCarrierTitle($rate->getCarrierTitle())
                ->setWarehouse($rate->getWarehouse())
                ->setWarehouseShippingDetails($rate->getWarehouseShippingDetails())
                ->setErrorMessage($rate->getErrorMessage())
            ;
        } elseif ($rate instanceof Mage_Shipping_Model_Rate_Result_Method) {
            $this
                ->setCode($rate->getCarrier().'_'.$rate->getMethod())
                ->setCarrier($rate->getCarrier())
                ->setCarrierTitle($rate->getCarrierTitle())
                ->setMethod($rate->getMethod())
                ->setWarehouse($rate->getWarehouse())
                ->setWarehouseShippingDetails($rate->getWarehouseShippingDetails())
                ->setExpectedDelivery($rate->getExpectedDelivery())
                ->setDispatchDate($rate->getDispatchDate())
                ->setFreightQuoteId($rate->getFreightQuoteId())
                ->setMethodTitle($rate->getMethodTitle())
                ->setMethodDescription($rate->getMethodDescription())
                ->setOverridePriceInfo($rate->getOverridePriceInfo())
                ->setPrice($rate->getPrice())
            ;
        }
        return $this;
    }
}