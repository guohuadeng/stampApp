<?php

class Magegiant_Dailydeal_Helper_Data extends Mage_Core_Helper_Abstract
{


	public function getConfig()
	{
		return Mage::getSingleton('dailydeal/config');
	}


	public function getTopLink()
	{
		return $this->_getUrl("dailydeal", array());
	}

	public function getDailydealTitle($title, $product_name, $save)
	{
		return str_replace(
			array(
				'{{product_name}}',
				'{{save}}'
			),
			array(
				$product_name,
				$save . '%'
			),
			$title
		);
	}

	public function random($value)
	{
		try {
			if (strpos($value, '-')) {
				$values = explode('-', $value);
				$arr    = range($values[0], $values[1]);
			} else {
				$arr = explode(';', $value);
			}
			$random = $arr[array_rand($arr, 1)];
		} catch (Exception $e) {
		}

		return $random;
	}
}