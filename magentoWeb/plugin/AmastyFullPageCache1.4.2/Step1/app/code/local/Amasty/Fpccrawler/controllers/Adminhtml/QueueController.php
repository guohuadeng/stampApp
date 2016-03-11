<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */
class Amasty_Fpccrawler_Adminhtml_QueueController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('report/amfpccrawler');
        $this->_addContent($this->getLayout()->createBlock('amfpccrawler/adminhtml_queue'));
        $this->renderLayout();
    }

    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        $this->_title($this->__('Reports'))->_title($this->__('FPC Crawler Queue'));

        return $this;
    }

    protected function _title($text = null, $resetIfExists = true)
    {
        if (Mage::helper('ambase')->isVersionLessThan(1, 4)) {
            return $this;
        }

        return parent::_title($text, $resetIfExists);
    }

    public function flushAction()
    {
        Mage::getResourceModel('amfpccrawler/queue')->flushQueue();
        $this->_redirect('*/*/index');

        return true;
    }
}
