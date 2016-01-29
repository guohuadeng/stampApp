<?php

/**
 * Productattachments extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Productattachments
 * @author     RT <rafay.tahir@unitedsol.net>
 * @copyright  Copyright 2015 � fmeextensions.com All right reserved
 */
class FME_Productattachments_Block_Adminhtml_Productattachments_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productattachmentsGrid');
        $this->setDefaultSort('productattachments_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('productattachments/productattachments')->getCollection();
        $collection->getSelect()
            ->join(
                array('cat' => Mage::getSingleton('core/resource')->getTableName('productattachments/productcats')),
                'main_table.cat_id=cat.category_id',
                array('cat.category_name')
            ); //echo (string) $collection->getSelect();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('productattachments_id', array(
            'header' => Mage::helper('productattachments')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'productattachments_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('productattachments')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));

        $this->addColumn('category_name', array(
            'header' => Mage::helper('productattachments')->__('Category'),
            'align' => 'left',
            'index' => 'category_name',
            //'filter_condition_callback' => array($this, '_categoryFilter'),
        ));

        $this->addColumn('downloads', array(
            'header' => Mage::helper('productattachments')->__('Downloads'),
            'align' => 'center',
            'index' => 'downloads',
            'type' => 'text',
            'width' => '100px',
        ));

        $this->addColumn('file_icon', array(
            'header' => Mage::helper('productattachments')->__('Type'),
            'align' => 'center',
            'index' => 'file_icon',
            'type' => 'text',
            'width' => '50px',
        ));

        $this->addColumn('file_size', array(
            'header' => Mage::helper('productattachments')->__('Size'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'file_size',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('productattachments')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('download_link', array(
            'header' => Mage::helper('productattachments')->__('Download'),
            'align' => 'center',
            'index' => 'download_link',
            'type' => 'text',
            'width' => '50px',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('productattachments')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('productattachments')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));


        $this->addExportType('*/*/exportCsv', Mage::helper('productattachments')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('productattachments')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _categoryFilter($collection, $column) { 
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()->addCategoryFilter($value); 
        
        //return $this;
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('productattachments_id');
        $this->getMassactionBlock()->setFormFieldName('productattachments');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('productattachments')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('productattachments')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('productattachments/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('productattachments')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('productattachments')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
