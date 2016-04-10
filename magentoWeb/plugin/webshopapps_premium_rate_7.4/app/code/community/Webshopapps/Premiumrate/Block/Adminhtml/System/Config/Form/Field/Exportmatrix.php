<?php
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Exampleextension
 * User         karen
 * Date         24/05/2013
 * Time         14:10
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Premiumrate_Block_Adminhtml_System_Config_Form_Field_Exportmatrix extends
    Webshopapps_Wsacommon_Block_Adminhtml_System_Config_Form_Field_Exportmatrix {


    protected function getCarrierCode() {
        return 'premiumrate';
    }


}