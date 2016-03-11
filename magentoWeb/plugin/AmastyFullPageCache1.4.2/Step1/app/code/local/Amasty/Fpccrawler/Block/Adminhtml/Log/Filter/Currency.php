<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */
class Amasty_Fpccrawler_Block_Adminhtml_Log_Filter_Currency extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    public function getCondition()
    {
        $value = $this->getValue();
        if (is_null($value) || $value == '') {
            return null;
        }

        return array('or' => array('eq' => $value));
    }
}