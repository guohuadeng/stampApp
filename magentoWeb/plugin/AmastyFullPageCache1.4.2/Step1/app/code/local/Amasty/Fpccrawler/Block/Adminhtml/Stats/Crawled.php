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
class Amasty_Fpccrawler_Block_Adminhtml_Stats_Crawled extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        $this->setHtmlId('crawled');
        $this->setTemplate('amasty/amfpccrawler/charts/crawled.phtml');

        parent::__construct();
    }

    protected function _toHtml()
    {
        $period  = 30;
        $crawled = Mage::getResourceModel('amfpccrawler/log')->getCrawledPages($period);
        $this->setCrawled($crawled);

        return parent::_toHtml();
    }
}
