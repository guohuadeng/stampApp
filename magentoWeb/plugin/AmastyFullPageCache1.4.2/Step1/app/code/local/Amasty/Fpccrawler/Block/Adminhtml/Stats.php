<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */
class Amasty_Fpccrawler_Block_Adminhtml_Stats extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $this->_controller = 'adminhtml_stats';
        $this->_blockGroup = 'amfpccrawler';
        $this->_headerText = Mage::helper('amfpccrawler')->__('FPC Visual Stats');
        $this->setTitle(Mage::helper('amfpccrawler')->__('FPC Visual Stats'));
        $this->setId('diagram_tab');
        $this->setDestElementId('diagram_tab_content');
        $this->setTemplate('amasty/amfpccrawler/tabshoriz.phtml');
    }

    protected function _prepareLayout()
    {
        $this->addTab('codes', array(
                'label'   => $this->__('Status Codes'),
                'content' => $this->getLayout()->createBlock('amfpccrawler/adminhtml_stats_codes')->toHtml(),
                'active'  => true
            )
        );

        $this->addTab('crawled', array(
                'label'   => $this->__('Pages Crawled'),
                'content' => $this->getLayout()->createBlock('amfpccrawler/adminhtml_stats_crawled')->toHtml(),
            )
        );

        $this->addTab('time', array(
                'label'   => $this->__('Page Load Time'),
                'content' => $this->getLayout()->createBlock('amfpccrawler/adminhtml_stats_time')->toHtml(),
            )
        );
        $this->addTab('queueProcessing', array(
                'label' => $this->__('Queue Processing'),
                'content' => $this->getLayout()->createBlock('amfpccrawler/adminhtml_stats_queueProcessing')->toHtml(),
            )
        );

        return parent::_prepareLayout();
    }
}