<?php

/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magegiant.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement.html
 */
class Magegiant_Dailydeal_Model_Config extends Mage_Core_Model_Abstract
{
	const XML_PATH_GENERAL_ENABLE = 'dailydeal/general/enable';
	const XML_PATH_GENERAL_LIMIT = 'dailydeal/general/limit';
	const XML_PATH_SIDEBAR_ENABLE = 'dailydeal/sidebar/enable';
	const XML_PATH_SIDEBAR_LEFT_LIMIT = 'dailydeal/sidebar/left_limit';
	const XML_PATH_SIDEBAR_RIGHT_LIMIT = 'dailydeal/sidebar/right_limit';
	const XML_PATH_SIDEBAR_SHOW_LEFT = 'dailydeal/sidebar/show_left';
	const XML_PATH_SIDEBAR_SHOW_RIGHT = 'dailydeal/sidebar/show_right';
	const XML_PATH_SIDEBAR_LEFT_DISPLAY_METHOD = 'dailydeal/sidebar/left_display_method';
	const XML_PATH_SIDEBAR_RIGHT_DISPLAY_METHOD = 'dailydeal/sidebar/right_display_method';
	const XML_PATH_SIDEBAR_LEFT_TITLE = 'dailydeal/sidebar/left_title';
	const XML_PATH_SIDEBAR_RIGHT_TITLE = 'dailydeal/sidebar/right_title';


	protected function getConfig($name)
	{
		if (!$name) return;
		$storeId = Mage::app()->getStore()->getId();

		return Mage::getStoreConfig($name, $storeId);
	}

	public function isEnabled()
	{
		return $this->getConfig(self::XML_PATH_GENERAL_ENABLE);
	}


	public function getDealLimit()
	{
		return $this->getConfig(self::XML_PATH_GENERAL_LIMIT);
	}

	public function getLeftLimit()
	{
		return $this->getConfig(self::XML_PATH_SIDEBAR_LEFT_LIMIT);

	}

	public function getRightLimit()
	{
		return $this->getConfig(self::XML_PATH_SIDEBAR_RIGHT_LIMIT);

	}


	public function canShowSidebar()
	{
		return $this->getConfig(self::XML_PATH_SIDEBAR_ENABLE);

	}

	public function canShowLeft()
	{
		return $this->getConfig(self::XML_PATH_SIDEBAR_SHOW_LEFT);

	}

	public function canShowRight()
	{
		return $this->getConfig(self::XML_PATH_SIDEBAR_SHOW_RIGHT);

	}


	public function getLeftDisplayMethod()
	{
		return $this->getConfig(self::XML_PATH_SIDEBAR_LEFT_DISPLAY_METHOD);

	}
	public function getRightDisplayMethod()
	{
		return $this->getConfig(self::XML_PATH_SIDEBAR_RIGHT_DISPLAY_METHOD);

	}

	public function getLeftTitle()
	{
		return $this->getConfig(self::XML_PATH_SIDEBAR_LEFT_TITLE);

	}

	public function getRightTitle()
	{
		return $this->getConfig(self::XML_PATH_SIDEBAR_RIGHT_TITLE);

	}




}