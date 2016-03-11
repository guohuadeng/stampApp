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
class Amasty_Fpccrawler_Block_Adminhtml_Stats_Codes extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        $this->setHtmlId('codes');
        $this->setTemplate('amasty/amfpccrawler/charts/codes.phtml');

        parent::__construct();
    }

    protected function _toHtml()
    {
        $period = 30;
        $codes  = Mage::getResourceModel('amfpccrawler/log')->getStatusCodes($period);
        $this->setCodes($codes);

        return parent::_toHtml();
    }
}
