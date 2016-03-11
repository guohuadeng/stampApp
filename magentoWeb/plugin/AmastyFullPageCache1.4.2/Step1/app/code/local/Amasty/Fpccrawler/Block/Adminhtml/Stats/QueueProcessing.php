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
class Amasty_Fpccrawler_Block_Adminhtml_Stats_QueueProcessing extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        $this->setHtmlId('processing');
        $this->setTemplate('amasty/amfpccrawler/charts/queueProcessing.phtml');

        parent::__construct();
    }

    protected function _toHtml()
    {
        $processing = Mage::getResourceModel('amfpccrawler/log')->getQueueProcessingTime();
        $this->setProcessing($processing);

        return parent::_toHtml();
    }
}
