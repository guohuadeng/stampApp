<?php

class Magegiant_Magegiantcore_Model_Observer
{
	public function controllerActionPredispatch($observer)
	{
		try{
			Mage::getModel('magegiantcore/magegiantcore')->checkUpdate();
		}catch(Exception $e){
		
		}
	}
	
}