<?php
/**
 * Created by  Achang WebDev
 *
 * @file Onepage.php
 * @author Owen <owen@achang.com>
 * @copyright Achang WebDev 
 * @link http://www.achang.com
 *
 *
 */
class Achang_ChinaCheckout_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{
    public function getSteps()
    {
        $steps = array();

        if (!$this->isCustomerLoggedIn()) {
            $steps['login'] = $this->getCheckout()->getStepData('login');
        }

        $stepCodes = array('billing',  'shipping_method', 'payment', 'review','shipping');

        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
            /*
            if ($step == 'billing') {
                $steps['billing']['label'] = $this->__('Shipping Information');
            }
            */
        }
        return $steps;
    }
}
