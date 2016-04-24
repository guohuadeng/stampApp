<?php
/**
 * Created by  Achang WebDev
 *
 * @file Billing.php
 * @author Owen <owen@achang.com>
 * @copyright Achang WebDev 
 * @link http://www.achang.com
 *
 * Date Time: 13-8-4 下午10:00
 */

class Achang_ChinaCheckout_Block_Checkout_Onepage_Billing  extends
    Mage_Checkout_Block_Onepage_Billing
{
    /**
     * Initialize billing address step
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->getCheckout()->setStepData('billing', array(
                'label'     => Mage::helper('checkout')->__('Shipping Information'),
                'is_show'   => $this->isShow()
            ));

        if ($this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('billing', 'allow', true);
        }
        //$this->setTemplate('removebilling/billing.phtml');
    }
}