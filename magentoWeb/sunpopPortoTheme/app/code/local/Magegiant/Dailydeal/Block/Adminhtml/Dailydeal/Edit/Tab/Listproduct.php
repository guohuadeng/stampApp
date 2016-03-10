<?php

class Magegiant_Dailydeal_Block_Adminhtml_Dailydeal_Edit_Tab_Listproduct extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('list_product_grid');
		$this->setDefaultSort('entity_id');
		$this->setUseAjax(true);
		if ($this->getDailydeal() && $this->getDailydeal()->getId()) {
			$this->setDefaultFilter(array('in_products' => 1));
		}
	}


	protected function _addColumnFilterToCollection($column)
	{
		if ($column->getId() == 'in_products') {
			$productIds = $this->_getSelectedProducts();
			if (empty($productIds)) {
				$productIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
			} else {
				if ($productIds) {
					$this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
				}
			}
		} else {
			parent::_addColumnFilterToCollection($column);
		}

		return $this;
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect('*')
			->addFieldToFilter('type_id', array('in' => array('simple', 'configurable', 'virtual', 'downloadable')))
			->addFieldToFilter('visibility', array('nin' => 1));
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{

		$this->addColumn('in_products', array(
			'header_css_class' => 'a-center',
			'type'             => 'radio',
			'html_name'        => 'aproducts[]',
			'align'            => 'center',
			'index'            => 'entity_id',
			'values'           => $this->_getSelectedProducts(),
		));

		$this->addColumn('entity_id', array(
			'header'   => Mage::helper('catalog')->__('ID'),
			'sortable' => true,
			'width'    => '60px',
			'index'    => 'entity_id'
		));

		$this->addColumn('name', array(
			'header' => Mage::helper('catalog')->__('Name'),
			'index'  => 'name'
		));

		$this->addColumn('type', array(
			'header'  => Mage::helper('catalog')->__('Type'),
			'width'   => 100,
			'index'   => 'type_id',
			'type'    => 'options',
			'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
		));

		$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
			->load()
			->toOptionHash();

		$this->addColumn('set_name',
			array(
				'header'  => Mage::helper('catalog')->__('Attrib. Set Name'),
				'width'   => '130px',
				'index'   => 'attribute_set_id',
				'type'    => 'options',
				'options' => $sets,
			));

		$this->addColumn('status',
			array(
				'header'  => Mage::helper('catalog')->__('Status'),
				'width'   => '90px',
				'index'   => 'status',
				'type'    => 'options',
				'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
			));

		$this->addColumn('visibility',
			array(
				'header'  => Mage::helper('catalog')->__('Visibility'),
				'width'   => '90px',
				'index'   => 'visibility',
				'type'    => 'options',
				'options' => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
			));

		$this->addColumn('sku', array(
			'header' => Mage::helper('catalog')->__('SKU'),
			'width'  => '80px',
			'index'  => 'sku'
		));

		$this->addColumn('price', array(
			'header'        => Mage::helper('catalog')->__('Price'),
			'type'          => 'currency',
			'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
			'index'         => 'price'
		));

		return parent::_prepareColumns();
	}

	protected function _getSelectedProducts()
	{
		$products = $this->getProduct();

		if (!is_array($products)) {
			$products = array_keys($this->getSelectedRelatedProducts());
		}

		return $products;
	}

	public function getSelectedRelatedProducts()
	{
		$products = array();

		$productIds = array($this->getDailydeal()->getProductId());

		foreach ($productIds as $productId) {

			$products[$productId] = array('position' => 0);

		}

		return $products;
	}

	public function getRowUrl($row)
	{
		return '#';
//		return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getId()));
	}

	public function getGridUrl()
	{
		return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/listproductgrid', array('_current' => true));
	}

	public function getDailydeal()
	{
		return Mage::getModel('dailydeal/dailydeal')
			->load($this->getRequest()->getParam('id'));
	}

}