<?php


class Sunpop_StampCustomer_Block_Adminhtml_Stampcustomer extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_stampcustomer";
	$this->_blockGroup = "stampcustomer";
	$this->_headerText = Mage::helper("stampcustomer")->__("Stampcustomer Manager");
	$this->_addButtonLabel = Mage::helper("stampcustomer")->__("Add New Item");
	parent::__construct();
	
	}

}