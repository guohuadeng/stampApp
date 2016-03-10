<?php
class Magegiant_Magegiantcore_Block_Adminhtml_License_Purchaseform
	extends Mage_Adminhtml_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('magegiantcore/license/purchaseform.phtml');
	}
	
	public function getPurchaseUrl()
	{
		return $this->getUrl('magegiantcore/adminhtml_license/purchase',array('extension'=>$this->getExtensionName(),'_secure'=>true));
	}
}