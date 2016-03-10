<?php

class Magegiant_Magegiantcore_Helper_License extends Mage_Core_Helper_Abstract
{
	public function getResumeCode($licensekey)
	{
		return md5($licensekey.'magegiant-extension-upgrade-license-59*47@');
	}
	
	public function getUpgradePrice($licensekey,$licensetype)
	{
		$resume = $this->getResumeCode($licensekey);
		/* try{
			$xmlRpc = new Zend_XmlRpc_Client(Magegiant_Magegiantcore_Model_Keygen::SERVER_URL.'api/xmlrpc/');
			$session = $xmlRpc->call('login', array('username'=>Magegiant_Magegiantcore_Model_Keygen::WEBSERVICE_USER,'password'=>Magegiant_Magegiantcore_Model_Keygen::WEBSERVICE_PASS));
			$result = $xmlRpc->call('call', array('sessionId' => $session,
												  'apiPath'   => 'licensemanager.getupgradeprice',
												  'args'      => array( $licensekey,
																		$licensetype,
																		$resume,
								)));
			if(!$result){ //error
				throw new Exception($this->__('Error! please try again.'));
				return;
			}
			return $result;
		} catch(Exception $e){
			throw new Exception($this->__('Error! please try again.').'<br/>'.$e->getMessage());
		}	 */		
	}
}