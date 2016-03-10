<?php

class Magegiant_Dailydeal_Block_Sidebar_Abstract extends Mage_Core_Block_Template
{
	public function _construct()
	{
		$this->setTemplate('magegiant/dailydeal/sidebar.phtml');

		return parent::_construct();
	}

	public function getDealByProduct($pid)
	{
		return Mage::getModel('dailydeal/dailydeal')->getDealByProduct($pid);
	}
}