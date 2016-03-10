<?php

class Smartwave_Porto_Helper_Cssconfig extends Mage_Core_Helper_Abstract
{
	protected $generatedCssFolder;
	protected $generatedCssPath;
	protected $generatedCssDir;
	
	public function __construct()
	{
		$this->generatedCssFolder = 'css/configed/';
		$this->generatedCssPath = 'frontend/smartwave/porto/' . $this->generatedCssFolder;
		$this->generatedCssDir = Mage::getBaseDir('skin') . '/' . $this->generatedCssPath;
	}
	
	public function getCssConfigDir()
    {
        return $this->generatedCssDir;
    }

	public function getSettingsFile()
	{
		return $this->generatedCssFolder . 'settings_' . Mage::app()->getStore()->getCode() . '.css';
	}
	
	public function getDesignFile()
	{
		return $this->generatedCssFolder . 'design_' . Mage::app()->getStore()->getCode() . '.css';
	}
}
