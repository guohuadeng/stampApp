<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_dropfreight
 * User         Joshua Stewart
 * Date         20/09/2013
 * Time         12:47
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Wsacommon_Block_Adminhtml_Sales_Order_Create_Shipping_Method_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form
{
    public function getLiftgateRequired()
    {
        if(Mage::getStoreConfig('shipping/wsafreightcommon/default_liftgate',Mage::app()->getStore()) && $this->getQuote()->getLiftgateRequired() == ''){
            return true;
        } else {
            return $this->getQuote()->getLiftgateRequired();
        }
    }

    public function getShiptoType()
    {
        return $this->getQuote()->getShiptoType();
    }

    public function getNotifyRequired()
    {
        return $this->getQuote()->getNotifyRequired();
    }


    public function getInsideDelivery()
    {
        return $this->getQuote()->getInsideDelivery();
    }


    public function getShiptoTypeHtmlSelect($defValue=null)
    {
        if (is_null($defValue)) {
            $defValue=$this->getShiptoType();
        }

        $options = Mage::helper('wsafreightcommon')->getOptions();

        $html = $this->getLayout()->createBlock('core/html_select')
            ->setName('shipto_type')
            ->setTitle(Mage::helper('wsafreightcommon')->__('Address Type'))
            ->setId('shipto_type')
            ->setClass('required-entry')
            ->setValue($defValue)
            ->setOptions($options)
            ->getHtml();
        return $html;

    }

    public function dontShowCommonFreight()
    {
        return Mage::helper('wsafreightcommon')->dontShowCommonFreight(
            $this->getQuote()->getAllItems(),$this->getQuote()->getShippingAddress()->getWeight());
    }
    /**
     * Added in for compatibility with AddressValidator
     * @param null $defValue
     * @return mixed
     */
    public function getDestTypeHtmlSelect($defValue=null)
    {
        if(Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsavalidation')){
            if (is_null($defValue)) {
                $defValue=$this->getQuote()->getDestType();
            }

            return Mage::helper('wsavalidation')->getBasicDestTypeHtmlSelect($this->getLayout(),$defValue);
        }
        return null;
    }
}
