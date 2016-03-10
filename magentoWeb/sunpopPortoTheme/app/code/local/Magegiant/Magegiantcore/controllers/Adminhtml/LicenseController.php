<?php

class Magegiant_Magegiantcore_Adminhtml_LicenseController extends Mage_Adminhtml_Controller_Action
{
	public function upgradeAction()
	{
		$licensekey = $this->getRequest()->getParam('licensekey');
		$licensetype = $this->getRequest()->getParam('licensetype');
		$redirectUrl = Magegiant_Magegiantcore_Model_Keygen::SERVER_URL.'licensemanager/license/upgrade/licensekey/'.$licensekey.'/licensetype/'.$licensetype;
		$this->_redirectUrl($redirectUrl);	
	}
	
	public function purchaseAction()
	{
		$extension = $this->getRequest()->getParam('extension');
		$redirectUrl = Magegiant_Magegiantcore_Model_Keygen::SERVER_URL.'licensemanager/license/purchase/extension/'.$extension;
		$this->_redirectUrl($redirectUrl);
	}
	
	public function viewpriceAction()
	{
		$licensekey = $this->getRequest()->getParam('licensekey');
		$licensetype = $this->getRequest()->getParam('licensetype');
		$upgradePrice = Mage::helper('magegiantcore/license')->getUpgradePrice($licensekey,$licensetype);
		$html = '<b>'.$upgradePrice.'</b>';
		$html .= ' '.Mage::helper('magegiantcore')->__('for upgrade to');
		$html .= ' '. Mage::getModel('magegiantcore/keygen')->getLicenseTitle($licensetype);
		$html .= '<br/><br/><button style="" onclick="updateLicensePurchase(\''.$licensekey.'\')" class="scalable add" type="button" >
				 <span>'.Mage::helper('magegiantcore')->__('Upgrade Now').'</span></button>';
		echo $html;
	}
}