<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */

/**
 * Adminhtml dashboard orders diagram
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Amasty_Fpccrawler_Block_Adminhtml_Stats_Time extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        $this->setHtmlId('time');
        $this->setTemplate('amasty/amfpccrawler/charts/time.phtml');

        parent::__construct();
    }

    protected function _toHtml()
    {
        $period = 30;
        $load   = Mage::getResourceModel('amfpccrawler/log')->getPageLoadTime($period);
        $this->setLoad($load);

        return parent::_toHtml();
    }
}
