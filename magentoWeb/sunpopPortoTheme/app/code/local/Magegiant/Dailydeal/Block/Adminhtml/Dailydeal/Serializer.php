<?php

class Magegiant_Dailydeal_Block_Adminhtml_Dailydeal_Serializer
	extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('magegiant/dailydeal/serializer.phtml');

		return $this;
	}

	public function initSerializerBlock($gridName, $hiddenInputName)
	{
		$grid = $this->getLayout()->getBlock($gridName);
		$this->setGridBlock($grid)
			->setInputElementName($hiddenInputName);
	}
}