<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */
class Amasty_Fpccrawler_Block_Adminhtml_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('queueGrid');
        $this->setDefaultSort('rate');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amfpccrawler/queue')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $hlp = Mage::helper('amfpccrawler');

        $this->addColumn('url', array(
            'header' => $hlp->__('URL'),
            'align'  => 'left',
            'index'  => 'url',
        )
        );
        $this->addColumn('rate', array(
            'header' => $hlp->__('Rate'),
            'align'  => 'left',
            'index'  => 'rate',
            'width'  => '20px',
        )
        );

        return parent::_prepareColumns();
    }

}