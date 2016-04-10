<?php
/* ProductMatrix
 *
 * @category   Webshopapps
 * @package    Webshopapps_productmatrix
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */


class Webshopapps_Premiumrate_Model_Carrier_Source_Postcode {

public function toOptionArray()
    {
        $productmatrix = Mage::getSingleton('premiumrate/carrier_premiumrate');
        $arr = array();
        foreach ($productmatrix->getCode('postcode_filtering') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>Mage::helper('shipping')->__($v));
        }
        return $arr;
    }
}
