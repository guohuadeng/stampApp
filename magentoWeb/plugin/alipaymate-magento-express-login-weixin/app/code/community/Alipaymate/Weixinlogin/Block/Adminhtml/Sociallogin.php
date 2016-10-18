<?php


class Alipaymate_Weixinlogin_Block_Adminhtml_Sociallogin extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_sociallogin";
	$this->_blockGroup = "weixinlogin";
	$this->_headerText = Mage::helper("weixinlogin")->__("Wechat Fans Manager");
	$this->_addButtonLabel = Mage::helper("weixinlogin")->__("Add New Item");
	parent::__construct();

	}

}
