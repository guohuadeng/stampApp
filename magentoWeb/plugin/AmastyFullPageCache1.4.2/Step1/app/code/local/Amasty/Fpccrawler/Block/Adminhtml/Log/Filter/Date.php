<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */
class Amasty_Fpccrawler_Block_Adminhtml_Log_Filter_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Range
{
    public function getCondition()
    {
        $value = $this->getValue();

        return $value;
    }

    public function getValue($index = null)
    {
        if ($index) {
            return $this->getData('value', $index);
        }

        $value = $this->getData('value');

        if (isset($value['from'])) {
            $value['from'] = strtotime($value['from']) - 86399;
        }
        if (isset($value['to'])) {
            $value['to'] = strtotime($value['to']) + 86399;
        }

        if ((isset($value['from']) && strlen($value['from']) > 0) || (isset($value['to']) && strlen($value['to']) > 0)) {
            return $value;
        }

        return null;
    }

}