<?php
/**
 * * NOTICE OF LICENSE
 * * This source file is subject to the Open Software License (OSL 3.0)
 *
 * Author: Ivan Deng
 * QQ: 300883
 * Email: 300883@qq.com
 * @copyright  Copyright (c) 2008-2015 Sunpop Ltd. (http://www.sunpop.cn)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
header('Access-Control-Allow-Origin: *');
header('P3P: CP=CAO PSA OUR');
class Sunpop_StampCustomer_IndexController extends Mage_Core_Controller_Front_Action{
    /* public function IndexAction() {

	  $this->loadLayout();
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Titlename"),
                "title" => $this->__("Titlename")
		   ));

      $this->renderLayout();

    }*/

	protected function _validateFormKey()
	{
		if (!($formKey = $this->getRequest()->getParam('form_key', null)) || $formKey != Mage::getSingleton('core/session')->getFormKey()) {
			return false;
		}
		return true;
	}

	public function ajaxdataAction(){
		$data = $this->getRequest()->getParams();
		/* $verification = Mage::helper("stampcustomer")->isVerification($data);
		switch($verification){
			case 1:
				$result['status'] = false;
				$result['message'] = urlencode($this->__('查询条件不能为空!'));
				$response = Mage::helper('core')->jsonEncode($result);
				$this->getResponse()->setBody(urldecode($response));
				return;
			case 2:
				$result['status'] = false;
				$result['message'] = urlencode($this->__('姓名至少2字!'));
				$response = Mage::helper('core')->jsonEncode($result);
				$this->getResponse()->setBody(urldecode($response));
				return;
			case 3:
				$result['status'] = false;
				$result['message'] = urlencode($this->__('公司至少2字!'));
				$response = Mage::helper('core')->jsonEncode($result);
				$this->getResponse()->setBody(urldecode($response));
				return;
		}*/

		try{
			$collection = Mage::getModel("stampcustomer/stampcustomer")->getCollection();
			if($data['a_state']){
				$collection->addFieldToFilter('a_state',  array('like' => "%".trim($data['a_state'])."%"));
			}
			if($data['a_certsn']){
				$collection->addFieldToFilter('a_certsn', array('like' => "%".trim($data['a_certsn'])."%"));
			}
			if($data['a_stampsn']){
				$collection->addFieldToFilter('a_stampsn', array('like' => "%".trim($data['a_stampsn'])."%"));
			}
			if($data['a_name']){
				//$names = Mage::helper('core/string')->splitWords($data['a_name']);
				$collection->addFieldToFilter('a_name', array('like' => "%".trim($data['a_name'])."%"));
			}
			if($data['a_company']){
				$collection->addFieldToFilter('a_company', array('like' => "%".trim($data['a_company'])."%"));
			}
			if($data['a_certtype']){
				$collection->addFieldToFilter('a_certtype', array('like' => "%".trim($data['a_certtype'])."%"));
			}
			if($data['a_certspec']){
				$collection->addFieldToFilter('a_certspec', array('like' => "%".trim($data['a_certspec'])."%"));
			}
			$collection->addFieldToFilter('status', array('eq' => 1));
			$collection->setPageSize(30)->setCurPage(1);


			if(count($collection)>0){
				$arrayobject = array();
				foreach($collection as $i=> $c){
					$result['a_name'] = urlencode($c->getAName());
					$result['a_state'] = urlencode($c->getAState());
					$result['a_certtype'] = urlencode($c->getACerttype());
					$result['a_certspec'] = urlencode($c->getACerttspec());
					$result['a_company'] = urlencode($c->getACompany());
					$result['a_certsn'] = urlencode($c->getACertsn());
					$result['a_stampsn'] = urlencode($c->getAStampsn());
					$result['a_validatesn'] = urlencode($c->getAVlidatesn());
					$result['a_expdate'] = urlencode($c->getAExpdate());
					$arrayobject[]= new ArrayObject($result);
				}
				$list['list'] = $arrayobject;
				$list['status'] = true;
				//$response = Mage::helper('core')->jsonEncode($list);
				$response = Mage::helper('core')->jsonEncode($collection);
				$this->getResponse()->setBody(urldecode($response));
				return;
			}else{
				$result['status'] = false;
				$result['message'] = urlencode($this->__('搜索结果为空，请更改搜索条件或者手工输入！'));
				$response = Mage::helper('core')->jsonEncode($result);
				$this->getResponse()->setBody(urldecode($response));
				return;
			}

		}catch(Exception $e){
			$result['status'] = false;
    		$result['message'] = urlencode($this->__('请重试!'));
			$response = Mage::helper('core')->jsonEncode($result);
			$this->getResponse()->setBody(urldecode($response));
    		return;
		}
		return ;
	}


	public function ajaxAction(){
		if(!$this->_validateFormKey()){
			 return $this->_redirect('*/*/');
		}
		$data = $this->getRequest()->getPost();
		$verification = Mage::helper("stampcustomer")->isVerification($data);

		switch($verification){
			case 1:
				$result['status'] = false;
				$result['message'] = '<h2 class="error">'.urlencode($this->__('查询条件不能为空!')).'</h2>';
				$response = Mage::helper('core')->jsonEncode($result);
				$this->getResponse()->setBody(urldecode($response));
				return;
			case 2:
				$result['status'] = false;
				$result['message'] = '<h2 class="error">'.urlencode($this->__('姓名至少2字!')).'</h2>';
				$response = Mage::helper('core')->jsonEncode($result);
				$this->getResponse()->setBody(urldecode($response));
				return;
			case 3:
				$result['status'] = false;
				$result['message'] = '<h2 class="error">'.urlencode($this->__('公司至少2字!')).'</h2>';
				$response = Mage::helper('core')->jsonEncode($result);
				$this->getResponse()->setBody(urldecode($response));
				return;

		}

		try{
			$collection = Mage::getModel("stampcustomer/stampcustomer")->getCollection();


			if($data['a_state']){
				$collection->addFieldToFilter('a_state',  array('like' => "%".trim($data['a_state'])."%"));
			}
			if($data['a_certsn']){
				$collection->addFieldToFilter('a_certsn', array('like' => "%".trim($data['a_certsn'])."%"));
			}
			if($data['a_stampsn']){
				$collection->addFieldToFilter('a_stampsn', array('like' => "%".trim($data['a_stampsn'])."%"));
			}
			if($data['a_name']){
				//$names = Mage::helper('core/string')->splitWords($data['a_name']);
				$collection->addFieldToFilter('a_name', array('like' => "%".trim($data['a_name'])."%"));
			}
			if($data['a_company']){
				$collection->addFieldToFilter('a_company', array('like' => "%".trim($data['a_company'])."%"));
			}
			if($data['a_certtype']){
				$collection->addFieldToFilter('a_certtype', array('like' => "%".trim($data['a_certtype'])."%"));
			}
			if($data['a_certspec']){
				$collection->addFieldToFilter('a_certspec', array('like' => "%".trim($data['a_certspec'])."%"));
			}
			$collection->addFieldToFilter('status', array('eq' => 1));
			$collection->setPageSize(20)->setCurPage(1);


			if(count($collection)>0){
				$html = '';
				$html .= '<div class="table-responsive"><table class="table table-condensed table-hover table-bordered">
						<tr>
							<th>序号</th>
							<th>姓名</th>
							<th>注册区域</th>
							<th>公司</th>
							<th>注册专业</th>
							<th>注册号</th>
							<th>印章编号</th>
							<th>校验码</th>
							<th>有效期至</th>
						</tr>';
				foreach($collection as $c){
					$html .= '<tr  class="info">';
					$html .= '<td class="a_id">'.$c->getAId().'</td>';
					$html .= '<td class="a_name">'.$c->getAName().'</td>';
					$html .= '<td class="a_state">'.$c->getAState().'</td>';
					$html .= '<td class="a_company">'.$c->getACompany().'</td>';
					$html .= '<td class="a_certspec">'.$c->getACertspec().'</td>';
					$html .= '<td class="a_certsn">'.$c->getACertsn().'</td>';
					$html .= '<td class="a_stampsn">'.$c->getAStampsn().'</td>';
					$html .= '<td class="a_validatesn">'.$c->getAValidatesn().'</td>';
					$html .= '<td class="a_expdate">'.$c->getAExpdate().'</td>';
					$html .= '</tr>';
				}
				$html .= '</table></div>';
				$html .='<script type="text/javascript">
				jQuery(function($){
					$(".info").click(function(){
						$(".info").css("color","#636363");
						$(this).css("color","red");
						var state = $(this).find(".a_state").html();
						var name = $(this).find(".a_name").html();
						var certtype = $(this).find(".a_certtype").html();
						var company = $(this).find(".a_company").html();
						var certspec = $(this).find(".a_certspec").html();
						var certsn = $(this).find(".a_certsn").html();
						var stampsn = $(this).find(".a_stampsn").html();
						var validatesn = $(this).find(".a_validatesn").html();
						var expdate = $(this).find(".a_expdate").html();
						$(".a_state").val(state);
						$(".a_name").val(name);
						$(".a_certtype").val(certtype);
						$(".a_company").val(company);
						$(".a_certspec").val(certspec);
						$(".a_certsn").val(certsn);
						$(".a_stampsn").val(stampsn);
						$(".a_validatesn").val(validatesn);
						if(expdate){
							$(".product-date").val(expdate);
						}
						//jQuery(".results").empty();
						jQuery(".stampcustomer").hide();
					})
					$(".sure").click(function(){
						$(".stampcustomer").hide();
					});
				});
				</script>';
				$result['status'] = true;
				$result['html'] = $html;
				$response = Mage::helper('core')->jsonEncode($result);
				$this->getResponse()->setBody($response);
				return;
			}else{
				$result['status'] = false;
				$result['message'] = '<h2 class="error">'.urlencode($this->__('搜索结果为空，请更改搜索条件或者手工输入！')).'</h2>';
				$response = Mage::helper('core')->jsonEncode($result);
				$this->getResponse()->setBody(urldecode($response));
				return;
			}

		}catch(Exception $e){
			$result['status'] = false;
    		$result['message'] = urlencode($this->__('请重试!'));
			$response = Mage::helper('core')->jsonEncode($result);
			$this->getResponse()->setBody(urldecode($response));
    		return;
		}
		return ;
	}
}
