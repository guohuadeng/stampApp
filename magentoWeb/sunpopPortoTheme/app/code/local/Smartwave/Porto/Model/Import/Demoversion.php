<?php

class Smartwave_Porto_Model_Import_Demoversion extends Mage_Core_Model_Abstract
{
	private $_importPath;
	
	public function __construct()
    {
        parent::__construct();
		$this->_importPath = Mage::getBaseDir() . '/app/code/local/Smartwave/Porto/etc/import/';
    }
	
	public function importDemoversion($demoversion,$store=NULL,$website = NULL)
    {
		try
		{
			$xmlPath = $this->_importPath . $demoversion . '.xml';
			if (!is_readable($xmlPath))
			{
				throw new Exception(
					Mage::helper('porto')->__("Can't get the data file for import demoversion: %s", $xmlPath)
                );
			}
			$xmlObj = new Varien_Simplexml_Config($xmlPath);
            $scope = "default";
            $scope_id = 0;
            if (strlen($store)) // store level
            {
                $store_id = Mage::getModel('core/store')->load($store)->getId();
                $scope = "stores";
                $scope_id = $store_id;
            }
            elseif (strlen($website)) // website level
            {
                $website_id = Mage::getModel('core/website')->load($website)->getId();
                $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
                $scope = "websites";
                $scope_id = $website_id;
            }
            else // default level
            {
                $store_id = 0;
            }
            $config = Mage::getConfig();
			foreach ($xmlObj->getNode("config")->children() as $b_name => $b)
			{
                foreach ($b->children() as $c_name => $c){
                    foreach ($c->children() as $d_name => $d){
                        $config->saveConfig($b_name.'/'.$c_name.'/'.$d_name,$c->$d_name,$scope,$scope_id);
                    }
                }
			}
			$config->cleanCache();

			Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('porto')
					->__('%s was imported',$demoversion)
            );
		}
		catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			Mage::logException($e);
		}
    }
	
}
