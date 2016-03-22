<?php
class Sunpop_StampCustomer_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getState(){
		$state = array(
		"" => "全部",
		"北京" => "北京",
		"天津" => "天津",
		"河北" => "河北",
		"山西" => "山西",
		"内蒙古" => "内蒙古",
		"辽宁" => "辽宁",
		"吉林" => "吉林",
		"黑龙江" => "黑龙江",
		"上海" => "上海",
		"江苏" => "江苏",
		"浙江" => "浙江",
		"安徽" => "安徽",
		"福建" => "福建",
		"江西" => "江西",
		"山东" => "山东",
		"河南" => "河南",
		"湖北" => "湖北",
		"湖南" => "湖南",
		"广东" => "广东",
		"广西" => "广西",
		"海南" => "海南",
		"深圳" => "深圳",
		"重庆" => "重庆",
		"四川" => "四川",
		"贵州" => "贵州",
		"云南" => "云南",
		"西藏" => "西藏",
		"陕西" => "陕西",
		"甘肃" => "甘肃",
		"青海" => "青海",
		"宁夏" => "宁夏",
		"新疆" => "新疆",
		"台湾" => "台湾",
		"香港" => "香港",
		"澳门" => "澳门",
		"总后基建营房部" => "总后基建营房部",
		);
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
	* @return int 1所有值为空，2名字不能小于两个字，3公司不能小于2个字
	*/
	public function isVerification($data){
		if(!$data){
			return 1;
		}
		if(!$data['a_name'] && !$data['a_company'] && !$data['a_certtype'] && !$data['a_certsn'] && !$data['a_stampsn']){
			return 1;
		}
		if(($data['a_name']) && (strlen($data['a_name'])<6)){
			return 2;
		}
		if(($data['a_company']) && (strlen($data['a_company'])<6)){
			return 3;
		}
		return ;
	}
}
