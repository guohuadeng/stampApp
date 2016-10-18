<?php

class Alipaymate_Weixinlogin_Block_Adminhtml_Sociallogin_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("socialloginGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("weixinlogin/sociallogin")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("weixinlogin")->__("ID"),
				"align" =>"right",
				"width" => "100",
			    "type" => "number",
				"index" => "id"
				));
				$this->addColumn('customer_id', array(
					'header' => Mage::helper('weixinlogin')->__('customer_id'),
					'align' => 'left',
					'index' => 'customer_id',
					'width'     => '70',
					'renderer' => 'Alipaymate_Weixinlogin_Block_Adminhtml_Template_Grid_Renderer_Customerid'
				));
				$this->addColumn("inside_weixin", array(
				"header" => Mage::helper("weixinlogin")->__("Inside Wechat"),
				"index" => "inside_weixin",
				"width"     => "80",
				"type" => "options",
				"options" => array('0'=>'网站扫码','1'=>'微信','3'=>'App')
				));
				$this->addColumn('sex', array(
					'header'    => Mage::helper('weixinlogin')->__('Sex'),
					'index'     => 'sex',
					'width'     => '50',
					'type' => 'options',
					'options' => array('0'=>'女','1'=>'男')
				));
				$this->addColumn("province", array(
				"header" => Mage::helper("weixinlogin")->__("Province"),
				"index" => "province",
				"width"     => "80"
				));
				$this->addColumn("city", array(
				"header" => Mage::helper("weixinlogin")->__("City"),
				"index" => "city",
				"width"     => "80"
				));
				$this->addColumn("unionid", array(
				"header" => Mage::helper("weixinlogin")->__("Unionid"),
				"index" => "unionid",
				));
				$this->addColumn("nickname", array(
				"header" => Mage::helper("weixinlogin")->__("Nickname"),
				"index" => "nickname"
				));
				$this->addColumn('headimgurl', array(
					'header' => Mage::helper('weixinlogin')->__('Headimgurl'),
					'align' => 'left',
					'index' => 'headimgurl',
					'width'     => '70',
					'renderer' => 'Alipaymate_Weixinlogin_Block_Adminhtml_Template_Grid_Renderer_Image'
				));

			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}



		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_sociallogin', array(
					 'label'=> Mage::helper('weixinlogin')->__('Remove Sociallogin'),
					 'url'  => $this->getUrl('*/sociallogin/massRemove'),
					 'confirm' => Mage::helper('weixinlogin')->__('Are you sure?')
				));
			return $this;
		}


}
