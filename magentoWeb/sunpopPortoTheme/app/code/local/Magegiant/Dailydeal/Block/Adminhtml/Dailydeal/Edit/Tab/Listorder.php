<?php

class Magegiant_Dailydeal_Block_Adminhtml_Dailydeal_Edit_Tab_Listorder extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('list_order_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('created_at');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Retrieve collection class
	 *
	 * @return string
	 */
	protected function _getCollectionClass()
	{
		return 'sales/order_collection';
	}

	protected function _prepareCollection()
	{
		if (!Mage::registry('deal')) {
			$deal = Mage::getModel('dailydeal/dailydeal')->load($this->getRequest()->getParam('id'));
			Mage::register('deal', $deal);
		} else {
			$deal = Mage::registry('deal');
		}
		$deal_id    = $deal->getId();
		$collection = Mage::getResourceModel($this->_getCollectionClass())
			->addFieldToFilter('dailydeals', array('finset' => $deal_id));
		$collection->getSelect()
			->joinLeft(array('order_item' => Mage::getModel('core/resource')->getTableName('sales_flat_order_item')), 'main_table.entity_id=order_item.order_id AND order_item.product_id=' . $deal->getProductId(), array('item_id', 'qty' => 'qty_ordered', 'total' => 'row_total'));
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}


	protected function _prepareColumns()
	{

		$this->addColumn('itemid', array(
			'header' => Mage::helper('sales')->__('Item ID'),
			'width'  => '80px',
			'type'   => 'text',
			'index'  => 'item_id',
		));
		$this->addColumn('real_order_id', array(
			'header' => Mage::helper('sales')->__('Order ID'),
			'width'  => '80px',
			'type'   => 'text',
			'index'  => 'increment_id',
		));

		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
				'header'          => Mage::helper('sales')->__('Purchased From (Store)'),
				'index'           => 'store_id',
				'type'            => 'store',
				'store_view'      => true,
				'display_deleted' => true,
				'width'           => '15%',
			));
		}

		$this->addColumn('created_at', array(
			'header' => Mage::helper('sales')->__('Purchased On'),
			'index'  => 'created_at',
			'type'   => 'datetime',
			'width'  => '15%',
		));
		$this->addColumn('qty', array(
			'header' => Mage::helper('sales')->__('Quantity'),
			'type'   => 'text',
			'width'  => '15%',
			'index'  => 'qty',
		));

		$this->addColumn('total', array(
			'header'   => Mage::helper('sales')->__('Total'),
			'type'     => 'currency',
			'width'    => '15%',
			'currency' => 'order_currency_code',
			'index'    => 'total',
		));

		$this->addColumn('status', array(
			'header'  => Mage::helper('sales')->__('Status'),
			'index'   => 'status',
			'type'    => 'options',
			'width'   => '70px',
			'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
		));

		return parent::_prepareColumns();
	}


	public function getRowUrl($row)
	{
		if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
			return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
		}

		return false;
	}

	public function getGridUrl()
	{
		return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/listordergrid', array('_current' => true));
	}

	public function getDailydeal()
	{
		return Mage::getModel('dailydeal/dailydeal')
			->load($this->getRequest()->getParam('id'));
	}
}