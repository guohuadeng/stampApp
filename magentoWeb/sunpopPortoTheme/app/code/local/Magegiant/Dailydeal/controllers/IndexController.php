<?php

class Magegiant_Dailydeal_IndexController extends Mage_Core_Controller_Front_Action
{
	protected function _initAction()
	{
		$this->loadLayout();
		$this->renderLayout();

		return $this;
	}

	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}