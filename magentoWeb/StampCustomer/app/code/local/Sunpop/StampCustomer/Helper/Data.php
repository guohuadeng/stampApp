<?php
class Sunpop_StampCustomer_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getState(){
		$collection = Mage::getModel("stampcustomer/stampcustomer")->getCollection();
		$state = array();
		foreach($collection as $c){
			$state[$c->getAState()] = $c->getAState();
		}
		return $state;
	}
	
	public function getOptionArray(){
		$status = array(
			array(
				'label'=>'Disable', 'value'=>'0'
			),
			array(
				'label'=>'Enable', 'value'=>'1'
			)
		);
		return $status;
	}
	/* 
	* @param array $data
	* @return int 1所有值为空，2名字不能小于两个字，3公司不能小于3个字
	*/
	public function isVerification($data){
		if(!$data){
			return 1;
		}
		if(!$data['a_state'] && !$data['a_name'] && !$data['a_company'] && !$data['a_certtype'] && !$data['a_certsn'] && !$data['a_stampsn']){
			return false;
		}  
		if(($data['a_name']) && (strlen($data['a_name'])<6)){
			return 2;
		}
		if(($data['a_company']) && (strlen($data['a_company'])<9)){
			return 3;
		}
		return ;
	}
}
	 