<?php
class Sunpop_StampCustomer_Block_Adminhtml_Stampcustomer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("stampcustomer_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("stampcustomer")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("stampcustomer")->__("Item Information"),
				"title" => Mage::helper("stampcustomer")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("stampcustomer/adminhtml_stampcustomer_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
