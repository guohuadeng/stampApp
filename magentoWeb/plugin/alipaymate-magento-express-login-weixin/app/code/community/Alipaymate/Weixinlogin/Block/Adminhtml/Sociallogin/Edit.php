<?php
	
class Alipaymate_Weixinlogin_Block_Adminhtml_Sociallogin_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "weixinlogin";
				$this->_controller = "adminhtml_sociallogin";
				$this->_updateButton("save", "label", Mage::helper("weixinlogin")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("weixinlogin")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("weixinlogin")->__("Save And Continue Edit"),
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
				if( Mage::registry("sociallogin_data") && Mage::registry("sociallogin_data")->getId() ){

				    return Mage::helper("weixinlogin")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("sociallogin_data")->getId()));

				} 
				else{

				     return Mage::helper("weixinlogin")->__("Add Item");

				}
		}
}