<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */
class Amasty_Fpccrawler_Block_Adminhtml_Queue extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_controller = 'adminhtml_queue';
        $this->_blockGroup = 'amfpccrawler';
        $this->_headerText = Mage::helper('amfpccrawler')->__('Queue');
        $this->_removeButton('add');
        $this->_addButton('flush', array(
                'label'   => 'Flush Queue',
                'onclick' => 'setLocation(\'' . Mage::helper("adminhtml")->getUrl("amfpccrawler/adminhtml_queue/flush") . '\')',
            )
        );
    }
}