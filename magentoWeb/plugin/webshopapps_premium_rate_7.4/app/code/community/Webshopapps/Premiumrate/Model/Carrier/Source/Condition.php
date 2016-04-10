<?php
/* ProductMatrix
 *
 * @category   Webshopapps
 * @package    Webshopapps_productmatrix
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */


class Webshopapps_Premiumrate_Model_Carrier_Source_Condition {

    public function toOptionArray()
    {
		
        $tableRate = Mage::getSingleton('premiumrate_shipping/carrier_premiumrate');
        $arr = array();
        foreach ($tableRate->getCode('condition_name') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
