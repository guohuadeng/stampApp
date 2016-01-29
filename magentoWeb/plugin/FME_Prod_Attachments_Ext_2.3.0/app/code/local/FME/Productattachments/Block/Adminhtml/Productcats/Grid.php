<?php

/**
 * Product Attachments extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Product Attachments
 * @copyright  Copyright 2010 � free-magentoextensions.com All right reserved
 * */
class FME_Productattachments_Block_Adminhtml_Productcats_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productscatsGrid');
        $this->setDefaultSort('category_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $res = Mage::getSingleton('core/resource');
        $storeTable = $res->getTableName('productattachments_category_store');

        $collection = Mage::getModel('productattachments/productcats')->getCollection();
        /* $collection->getSelect()
          ->join(array('store' => $storeTable), 'main_table.category_id = store.category_id');
          echo (string) $collection->getSelect();exit;
          foreach($collection as $link){

          if($link->getStoreId() && $link->getStoreId() != 0 ){

          $link->setStoreId(array($link->getStoreId()));
          } else{

          $link->setStoreId(array('0'));
          }
          } */

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('category_id', array(
            'header' => Mage::helper('productattachments')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'category_id',
        ));

        $this->addColumn('category_name', array(
            'index' => 'category_name',
            'type' => 'text',
            'header' => $this->__('Title'),
            'filter_condition_callback' => array($this, '_filterCategory'),
        ));

        $options = Mage::helper('productattachments')->parentTitleOptions();
        $this->addColumn('parent_category_id', array(
            'header' => Mage::helper('productattachments')->__('Parent Category'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'parent_category_id',
            'type' => 'options',
            'options' => $options,
            'renderer' => 'FME_Productattachments_Block_Adminhtml_Renderer_ParentCategory',
        ));


        /* if (!Mage::app()->isSingleStoreMode()) {
          $this->addColumn('store_id', array(
          'header'        => Mage::helper('productattachments')->__('Store View'),
          'index'         => 'store_id',
          'type'          => 'store',
          'store_all'     => true,
          'store_view'    => true,
          'sortable'      => true,
          'filter_condition_callback' => array($this,
          '_filterStoreCondition'),
          ));
          } */

        $this->addColumn('category_status', array(
            'header' => Mage::helper('productattachments')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'category_status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));


        $this->addColumn('action', array(
            'header' => $this->__('Action'),
            'width' => '80px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $this->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'category_id',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('category_id');
        $this->getMassactionBlock()->setFormFieldName('category_ids');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => $this->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => $this->__('Are you sure?')
        ));
        $statuses = array();
        $statuses = FME_Productattachments_Model_Status::getOptionArray();
        array_unshift($statuses, array('label' => '', 'value' => ''));

        $this->getMassactionBlock()->addItem('status', array(
            'label' => $this->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => $this->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getCategoryId()));
    }

    protected function _filterStoreCondition($collection, $column) {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
    }

    /*
     * Category filter callback
     * @see Mage_Adminhtml_Block_Widget_Grid::_addColumnFilterToCollection()
     */

    protected function _filterCategory($collection, $column) {
        if (!$value = $column->getFilter()->getValue())
            return;
        if (isset($value))
            $collection->getSelect()
                    ->where('main_table.category_name LIKE \'%' . mysql_escape_string($value) . '%\' OR '
                            . 'main_table.category_name LIKE \'%' . mysql_escape_string($value) . '%\'');
    }

    /*
     * URLKey filter callback
     * @see Mage_Adminhtml_Block_Widget_Grid::_addColumnFilterToCollection()
     */

    protected function _filterURLKey($collection, $column) {
        if (!$value = $column->getFilter()->getValue())
            return;
        if (isset($value))
            $collection->getSelect()
                    ->where('main_table.category_url_key LIKE \'%' . mysql_escape_string($value) . '%\' OR '
                            . 'main_table.category_url_key LIKE \'%' . mysql_escape_string($value) . '%\'');
    }

    /*
     * Status filter callback
     * @see Mage_Adminhtml_Block_Widget_Grid::_addColumnFilterToCollection()
     */

    protected function _filterStatus($collection, $column) {
        if (!$value = $column->getFilter()->getValue())
            return;
        if (isset($value))
            $collection->getSelect()
                    ->where('main_table.category_status = ' . mysql_escape_string($value));
    }

}
