<?php

class Magegiant_Dailydeal_Block_Adminhtml_Dailydeal_Renderer_Dailydealavailable extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$dealId = $row->getProductId();
		$str    = '';
		$deal   = Mage::getModel('dailydeal/dailydeal')->load($dealId);
		if (!$dealId) {
			$str = $this->__('There is no daily deal available!');
		} else {
			$str .= '<a href="' . $this->getUrl('dailydealadmin/adminhtml_dailydeal/edit/', array('id' => $dealId)) . '">' . $deal->getTitle() . '</a></br>';
		}

		return $str;
	}
}