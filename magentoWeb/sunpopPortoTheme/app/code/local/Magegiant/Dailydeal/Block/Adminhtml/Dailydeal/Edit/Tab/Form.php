<?php

class Magegiant_Dailydeal_Block_Adminhtml_Dailydeal_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getDeal()
	{
		if (!$this->hasData('dailydeal_data')) {
			$this->setData('dailydeal_data', Mage::registry('dailydeal_data'));
		}

		return $this->getData('dailydeal_data');
	}


	public function getStatus()
	{
		return array(
//			1 => 'Scheduled',
			3 => 'Active',
			4 => 'Expired',
			2 => 'Disable',
		);

	}


	public function getProduct($id)
	{
		return Mage::getSingleton('catalog/product')->load($id);
	}

	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('dailydeal_edit', array('legend' => Mage::helper('dailydeal')->__('Deal information')));

		$data       = $this->getDeal();

		$fieldset->addField('title', 'text', array(
			'label'    => Mage::helper('dailydeal')->__('Deal Name'),
			'class'    => 'required-entry',
			'required' => true,
			'name'     => 'title',
			'note'     => '',
		));
		$note = '';
		if ($data['product_id'])
			$note .= ' <a target="_blank" href="' . $this->getUrl('adminhtml/catalog_product/edit', array('id' => $data->getProductId())) . '">' . $this->__('Edit Product') . '</a>
							<input type="hidden" name="product_id" id="product_id" value="' . $data->getProductId() . '">';
		else
			$note = '<a target="_blank" href="javascript:void(0);" onclick="showSelectProductTab();">' . $this->__('Select a product') . '</a> ';

		$fieldset->addField('product_name', 'text', array(
			'label'    => Mage::helper('dailydeal')->__('Product'),
			'class'    => 'required-entry',
			'required' => true,
			'readonly' => 'readonly',
			'name'     => 'product_name',
			'disabled' => true,
			'note'     => $note,
		));

		if ($data['product_id']) {
			$product = $this->getProduct($data['product_id']);
			$fieldset->addField('product_price', 'label', array(
				'value'              => $this->__('Product Price'),
				'label'              => $this->__('Product Price'),
				'after_element_html' => Mage::helper('core')->currency($product->getPrice(), true, false),
			));

			$fieldset->addField('product_qty', 'label', array(
				'value'              => $this->__('Product Qty'),
				'label'              => $this->__('Product Qty'),
				'after_element_html' => number_format($product->getStockItem()->getQty()),
				'note'               => 'If you select a new product, you should click on Save Deal to get new price and Qty.'

			));


		}

//		$fieldset->addField('thumbnail_image', 'image', array(
//			'label'    => Mage::helper('dailydeal')->__('Deal Image'),
//			'required' => false,
//			'note'     => 'Deal Thumbnail image',
//			'name'     => 'thumbnail',
//			'disabled' => $isDisabled,
//		));

		$fieldset->addField('save', 'text', array(
			'label'    => Mage::helper('dailydeal')->__('Deal Discount'),
			'class'    => 'required-entry',
			'required' => true,
			'name'     => 'save',
			'note'     => 'Example 10 (as 10%)',
		));

		$fieldset->addField('quantity', 'text', array(
			'label'    => Mage::helper('dailydeal')->__('Deal Qty'),
			'class'    => 'required-entry',
			'required' => true,
			'name'     => 'quantity',

		));
		try {
			$data['start_time'] = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(strtotime($data['start_time'])));
			$data['close_time'] = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(strtotime($data['close_time'])));
		} catch (Exception $e) {

		}
		$note = $this->__('The current server time is') . ': ' . $this->formatTime(now(), Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, true);
		$fieldset->addField('start_time', 'date', array(
			'label'        => Mage::helper('dailydeal')->__('Start From'),
			'name'         => 'start_time',
			'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
			'image'        => $this->getSkinUrl('images/grid-cal.gif'),
			'format'       => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
			'time'         => true,
			'required'     => true,
		));
		$fieldset->addField('close_time', 'date', array(
			'label'        => Mage::helper('dailydeal')->__('End To'),
			'name'         => 'close_time',
			'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
			'image'        => $this->getSkinUrl('images/grid-cal.gif'),
			'format'       => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
			'time'         => true,
			'required'     => true,
			'note'         => $note,
		));
		if (Mage::app()->isSingleStoreMode()) {
			$fieldset->addField('store_id', 'hidden', array(
				'name'  => 'stores[]',
				'value' => Mage::app()->getStore(true)->getId(),
			));
			$data['store_id'] = Mage::app()->getStore(true)->getId();
		} else {
			$fieldset->addField('store_id', 'multiselect', array(
				'name'     => 'stores[]',
				'label'    => Mage::helper('dailydeal')->__('Store'),
				'title'    => Mage::helper('dailydeal')->__('Store'),
				'required' => true,
				'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));
		}

		$fieldset->addField('status', 'select', array(
			'label'  => Mage::helper('dailydeal')->__('Status'),
			'name'   => 'status_form',
			'values' => $this->getStatus(),
		));

		if (Mage::getSingleton('adminhtml/session')->getDailydealData()) {
			$data = Mage::getSingleton('adminhtml/session')->getDailydealData();
			Mage::getSingleton('adminhtml/session')->setDailydealData(null);
		} elseif (Mage::registry('dailydeal_data')) {
			$data = Mage::registry('dailydeal_data')->getData();
		}
		if ($data) {
			if (isset($data['product_id']) && $data['product_id']) {
				$product              = Mage::getModel('catalog/product')->load($data['product_id']);
				$data['product_name'] = $product->getName();
			}
			$form->setValues($data);
		}

		return parent::_prepareForm();

	}
}