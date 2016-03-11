<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */

class Amasty_Fpc_Model_Config_Source_Compression
{
    const COMPRESSION_NONE = 0;
    const COMPRESSION_PAGES = 1;
    const COMPRESSION_ALL = 2;

    public function toOptionArray()
    {
        $hlp = Mage::helper('amfpc');
        $vals = array(
            self::COMPRESSION_NONE   => $hlp->__('None'),
            self::COMPRESSION_PAGES  => $hlp->__('Compress pages'),
            self::COMPRESSION_ALL    => $hlp->__('Compress pages and blocks'),
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
