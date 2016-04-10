<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Common
 * User         Joshua Stewart
 * Date         20/09/2013
 * Time         11:56
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Wsacommon_Helper_Template extends Mage_Core_Helper_Abstract
{
    public function adminTemplate($ref)
    {
        switch ($ref) {
            case 'adminhtml_sales_order_create_index';
            case 'adminhtml_sales_order_create_load_block_data';
            case 'adminhtml_sales_order_create_load_block_shipping_method';

            if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsafreightcommon', 'shipping/wsafreightcommon/active')
                && Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Adminshipping')) {
                return 'webshopapps/wsafreightcommon/sales/order/create/shipping/method/formcombineadmin.phtml';
            }

            if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsafreightcommon', 'shipping/wsafreightcommon/active')) {
                return 'webshopapps/wsafreightcommon/sales/order/create/shipping/method/form.phtml';
            }

            if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Adminshipping')) {
                return 'webshopapps_adminshipping/order/create/shipping/method/form.phtml';
            }

            break;

            case 'order_tab_info':
                if(Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsafreightcommon','shipping/wsafreightcommon/active')
                    && Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Dropship','carriers/dropship/active')) {
                    return 'webshopapps/wsafreightcommon/sales/order/view/tab/info_containercombinedrop.phtml';
                }

                if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsafreightcommon','shipping/wsafreightcommon/active')) {
                    return 'webshopapps/wsafreightcommon/sales/order/view/tab/info_container.phtml';
                }

                if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Dropship','carriers/dropship/active')) {
                    return 'webshopapps/dropship/sales/order/view/tab/info_container.phtml';
                }

                break;

            default: return '';
        }

        return "";
    }
}