<?php

class Magegiant_Dailydeal_Model_System_Config_Display extends Varien_Object
{
	const RANDOM = 1;
	const LATEST = 2;
	const TODAY = 3;
	const THIS_WEEK = 4;
	const THIS_MONTH = 5;


	static public function getOptionArray()
	{
		return array(
			self::RANDOM     => Mage::helper('dailydeal')->__('Random'),
			self::LATEST     => Mage::helper('dailydeal')->__('Latest'),
			self::TODAY      => Mage::helper('dailydeal')->__('Today'),
			self::THIS_WEEK  => Mage::helper('dailydeal')->__('This Week'),
			self::THIS_MONTH => Mage::helper('dailydeal')->__('This Month'),
		);
	}

	public function toOptionArray()
	{
		return self::getOptionHash();
	}

	static public function getOptionHash()
	{
		$options = array();
		foreach (self::getOptionArray() as $value => $label)
			$options[] = array(
				'value' => $value,
				'label' => $label
			);

		return $options;
	}
}