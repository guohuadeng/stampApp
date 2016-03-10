<?php

class Magegiant_Magegiantcore_Adminhtml_Magegiantcore_SupportController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction() {
		$this->getResponse()->setRedirect('http://support.magegiant.com/');
	}
	
}