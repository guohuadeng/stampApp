<?php

class Smartwave_Porto_Model_System_Config_Backend_Design_Color_Validatetransparent extends Mage_Core_Model_Config_Data
{
	public function save()
	{
		$v = $this->getValue();
		if ($v == 'rgba(0, 0, 0, 0)')
		{
			$this->setValue('transparent');
		}
		return parent::save();
	}
}
