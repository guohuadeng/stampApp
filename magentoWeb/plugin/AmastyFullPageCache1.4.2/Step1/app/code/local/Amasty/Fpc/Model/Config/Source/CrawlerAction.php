<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */

class Amasty_Fpc_Model_Config_Source_CrawlerAction
{
    const ACTION_NONE = 0;
    const ACTION_REGENERATE = 1;
    const ACTION_REFRESH = 2;

    public function toOptionArray()
    {
        $hlp = Mage::helper('amfpc');
        $vals = array(
            self::ACTION_NONE        => $hlp->__('None'),
            self::ACTION_REGENERATE  => $hlp->__('Regenerate cache lifetime'),
            self::ACTION_REFRESH     => $hlp->__('Re-crawl page'),
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
