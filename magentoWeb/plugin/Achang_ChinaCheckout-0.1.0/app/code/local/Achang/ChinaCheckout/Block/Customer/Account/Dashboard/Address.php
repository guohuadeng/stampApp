<?php
/**
 * Created by  Achang WebDev
 *
 * @file Address.php
 * @author Owen <owen@achang.com>
 * @copyright Achang WebDev 
 * @link http://www.achang.com
 *
 * Date Time: 13-8-4 下午10:47
 */

class Achang_ChinaCheckout_Block_Customer_Account_Dashboard_Address
    extends  Mage_Customer_Block_Account_Dashboard_Address
{

    /*
    public function _construct(){
        $this->setTemplate('removebilling/dashboard/addresss.phtml');
    }
    */

    public function getPrimaryBillingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryBillingAddress();

        if( $address instanceof Varien_Object ) {
            return $address->format('html');
        } else {
            return Mage::helper('customer')->__('You have not set a default shipping address.');
        }
    }
}