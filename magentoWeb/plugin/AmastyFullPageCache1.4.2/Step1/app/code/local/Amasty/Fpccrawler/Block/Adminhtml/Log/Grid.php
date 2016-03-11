<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */
class Amasty_Fpccrawler_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('logGrid');
        $this->setDefaultSort('log_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amfpccrawler/log')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $hlp = Mage::helper('amfpccrawler');

        $groups     = Mage::getResourceModel('customer/group_collection')
                          ->addFieldToFilter('customer_group_id', array('gt' => 0))
                          ->load()
                          ->toOptionHash();
        $currencies = Mage::getModel('core/config_data')
                          ->getCollection()
                          ->addFieldToFilter('path', 'currency/options/allow')
                          ->getData();
        if (is_array($currencies) && isset($currencies[0]['value'])) {
            $currencies = explode(',', $currencies[0]['value']);
        }
        foreach ($currencies as $value) {
            $currenciesOptions[$value] = $value;
        }

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
                //'filter' => 'amfpccrawler/adminhtml_log_filter_rate',
                'type' => 'range',
            )
        );
        $this->addColumn('store', array(
                'header'     => $hlp->__('Store'),
                'align'      => 'left',
                'index'      => 'store',
                'type'       => 'store',
                'store_all'  => true,
                'store_view' => true,
                'filter'     => 'amfpccrawler/adminhtml_log_filter_store'
            )
        );
        $this->addColumn('currency', array(
                'header'  => $hlp->__('Currency'),
                'align'   => 'left',
                'index'   => 'currency',
                'type'    => 'options',
                'options' => $currenciesOptions,
                'width'   => '20px',
                'filter'  => 'amfpccrawler/adminhtml_log_filter_currency',
            )
        );
        $this->addColumn('customer_group', array(
                'header'  => $hlp->__('Customer Group'),
                'align'   => 'left',
                'index'   => 'customer_group',
                'type'    => 'options',
                'options' => $groups,
                'width'   => '50px',
            )
        );
        $this->addColumn('mobile', array(
                'header'  => $hlp->__('Mobile'),
                'align'   => 'left',
                'index'   => 'mobile',
                'width'   => '10px',
                'type'    => 'options',
                'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
                'filter'  => 'amfpccrawler/adminhtml_log_filter_mobile'
            )
        );
        $this->addColumn('status', array(
                'header'   => $hlp->__('Status'),
                'align'    => 'left',
                'index'    => 'status',
                'width'    => '20px',
                'renderer' => 'amfpccrawler/adminhtml_log_renderer_status',
                'filter'   => 'amfpccrawler/adminhtml_log_filter_status',
            )
        );
        $this->addColumn('page_load', array(
                'header' => $hlp->__('Load time (seconds)'),
                'align'  => 'left',
                'index'  => 'page_load',
                'width'  => '10px',
                'type' => 'range',
            )
        );
        $this->addColumn('date', array(
                'header'   => $hlp->__('Crawl date'),
                'align'    => 'left',
                'index'  => 'datetime',
                'width'  => '60px',
                'type'   => 'date',
                'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                //'renderer' => 'amfpccrawler/adminhtml_log_renderer_date',
                //'filter'   => 'amfpccrawler/adminhtml_log_filter_date',
            )
        );

        return parent::_prepareColumns();
    }

}