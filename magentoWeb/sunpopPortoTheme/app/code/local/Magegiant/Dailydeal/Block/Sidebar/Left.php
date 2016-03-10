<?php

class Magegiant_Dailydeal_Block_Sidebar_Left extends Magegiant_Dailydeal_Block_Sidebar_Abstract
{

	public function getSidebarDeals()
	{
		return Mage::getModel('dailydeal/dailydeal')->getLeftSidebar();
	}


	public function getTitle()
	{
		return Mage::helper('dailydeal')->getConfig()->getLeftTitle();
	}

	public function getType()
	{
		return 'left';
	}
}