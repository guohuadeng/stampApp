<?php

class Magegiant_Dailydeal_Block_Sidebar extends Mage_Core_Block_Template
{
	protected $_config;

	public function _construct()
	{
		return parent::_construct();
	}


	public function getlink()
	{
		$link = Mage::app()->getRequest()->getControllerName() .
			Mage::app()->getRequest()->getActionName() .
			Mage::app()->getRequest()->getRouteName() .
			Mage::app()->getRequest()->getModuleName();

		return $link;
	}

	protected function _canShow()
	{
		$config = Mage::helper('dailydeal')->getConfig();
		$router = Mage::app()->getRequest()->getControllerName() .
			Mage::app()->getRequest()->getActionName() .
			Mage::app()->getRequest()->getRouteName() .
			Mage::app()->getRequest()->getModuleName();

		return ($this->getlink() == $router) AND $config->canShowSidebar();
	}

	public function getLeft()
	{
		$config = Mage::helper('dailydeal')->getConfig();
		$block = $this->getParentBlock();

		if ($block) {
			if ($this->_canShow() AND $config->canShowLeft()) {

				$sidebarBlock = $this->getLayout()
					->createBlock('dailydeal/sidebar_left');
				$block->insert($sidebarBlock, '', false, 'dailydeal-sidebar-left');
			}
		}
	}

	public function getRight()
	{
		$config = Mage::helper('dailydeal')->getConfig();
		$block = $this->getParentBlock();
		if ($block) {
			if ($this->_canShow() AND $config->canShowRight()) {
				$sidebarBlock = $this->getLayout()
					->createBlock('dailydeal/sidebar_right');
				$block->insert($sidebarBlock, '', false, 'dailydeal-sidebar-right');
			}
		}
	}


}