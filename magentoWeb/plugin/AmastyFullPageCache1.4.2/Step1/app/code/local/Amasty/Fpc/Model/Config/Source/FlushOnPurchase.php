<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */

class Amasty_Fpc_Model_Config_Source_FlushOnPurchase
{
    const ACTION_NO = 0;
    const ACTION_PRODUCT_ONLY = 1;
    const ACTION_ASSOCIATED = 2;

    public function toOptionArray()
    {
        $hlp = Mage::helper('amfpc');
        $vals = array(
            self::ACTION_NO             => $hlp->__('No'),
            self::ACTION_PRODUCT_ONLY   => $hlp->__('Flush only product page'),
            self::ACTION_ASSOCIATED     => $hlp->__('Also flush associated pages'),
        );

        $options = array();
        foreach ($vals as $k => $v)
            $options[] = array(
                'value' => $k,
                'label' => $v
            );

        return $options;
    }
}
