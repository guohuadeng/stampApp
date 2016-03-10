<?php

class Magegiant_Dailydeal_Block_Adminhtml_Dailydeal_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('dailydeal_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('dailydeal')->__('Deal Information'));
	}

	protected function _beforeToHtml()
	{
		$deal = $this->getDailydeal();

		if (!$deal || ($deal && ($deal->getStatus() == ''))) {
			$this->addTab('form_listproduct', array(
				'label' => Mage::helper('dailydeal')->__('Select a Product'),
				'title' => Mage::helper('dailydeal')->__('Select a Product'),
				'class' => 'ajax',
				'url'   => $this->getUrl('*/*/listproduct', array('_current' => true, 'id' => $this->getRequest()->getParam('id'))),
//				'content' => $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_edit_tab_listproduct')->toHtml(),

			));
		}

		$this->addTab('form_section', array(
			'label'   => Mage::helper('dailydeal')->__('Deal Information'),
			'title'   => Mage::helper('dailydeal')->__('Deal Information'),
			'content' => $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_edit_tab_form')->toHtml(),
		));


		if ($deal) {
			$this->addTab('form_listproduct', array(
				'label' => Mage::helper('dailydeal')->__('Select a Product'),
				'title' => Mage::helper('dailydeal')->__('Select a Product'),
				'class' => 'ajax',
				'url'   => $this->getUrl('*/*/listproduct', array('_current' => true, 'id' => $this->getRequest()->getParam('id'))),
//				'content' => $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_edit_tab_listproduct')->toHtml(),

			));
		}

		if (!$deal || ($deal && ($deal->getStatus() != ''))) {
			$this->addTab('form_listorder', array(
				'label'   => Mage::helper('dailydeal')->__('Sold'),
				'title'   => Mage::helper('dailydeal')->__('Sold'),
				'content' => $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_edit_tab_listorder')->toHtml(),
			));
		}

		return parent::_beforeToHtml();
	}

	public function getDailydeal()
	{
		if (!$this->hasData('dailydeal_data')) {
			$this->setData('dailydeal_data', Mage::registry('dailydeal_data'));
		}

		return $this->getData('dailydeal_data');
	}
}