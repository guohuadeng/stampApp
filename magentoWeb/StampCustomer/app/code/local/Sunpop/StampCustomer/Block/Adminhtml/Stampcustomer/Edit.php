<?php
	
class Sunpop_StampCustomer_Block_Adminhtml_Stampcustomer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "a_id";
				$this->_blockGroup = "stampcustomer";
				$this->_controller = "adminhtml_stampcustomer";
				$this->_updateButton("save", "label", Mage::helper("stampcustomer")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("stampcustomer")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("stampcustomer")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("stampcustomer_data") && Mage::registry("stampcustomer_data")->getId() ){

				    return Mage::helper("stampcustomer")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("stampcustomer_data")->getId()));

				} 
				else{

				     return Mage::helper("stampcustomer")->__("Add Item");

				}
		}
}