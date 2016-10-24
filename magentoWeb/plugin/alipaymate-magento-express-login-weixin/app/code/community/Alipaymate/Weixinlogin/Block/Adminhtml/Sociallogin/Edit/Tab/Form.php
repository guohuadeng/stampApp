<?php
class Alipaymate_Weixinlogin_Block_Adminhtml_Sociallogin_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("weixinlogin_form", array("legend"=>Mage::helper("weixinlogin")->__("Item information")));




						$fieldset->addField("customer_id", "text", array(
						"label" => Mage::helper("weixinlogin")->__("Customer Id"),
						"name" => "customer_id",
						));

						$fieldset->addField("inside_weixin", "select", array(
						"label" => Mage::helper("weixinlogin")->__("Inside Wechat"),
						"name" => "inside_weixin",
						"values"    => array('0'=>'网站扫码','1'=>'微信','3'=>'App'),
						"disabled"  => true
						));


						$fieldset->addField("nickname", "text", array(
						"label" => Mage::helper("weixinlogin")->__("Nickname"),
						"name" => "nickname",
						"disabled"  => true
						));

						$fieldset->addField('sex', 'select', array(
							'name'      => 'sex',
							'label'     => Mage::helper('weixinlogin')->__('Sex'),
							'title'     => Mage::helper('weixinlogin')->__('Sex'),
							'values'    => array('1'=>Mage::helper('weixinlogin')->__('男'),'0'=>Mage::helper('weixinlogin')->__('女')),
              'disabled'  => true
						));

						$fieldset->addField("city", "text", array(
						"label" => Mage::helper("weixinlogin")->__("City"),
						"name" => "city",
						"disabled"  => true
						));

						$fieldset->addField("province", "text", array(
						"label" => Mage::helper("weixinlogin")->__("Province"),
						"name" => "province",
						"disabled"  => true
						));

						$fieldset->addField("country", "text", array(
						"label" => Mage::helper("weixinlogin")->__("Country"),
						"name" => "country",
						"disabled"  => true
						));
						$fieldset->addField("openid", "text", array(
						"label" => Mage::helper("weixinlogin")->__("Openid"),
						"name" => "openid",
						"disabled"  => true
						));
						$fieldset->addField("unionid", "text", array(
						"label" => Mage::helper("weixinlogin")->__("Unionid"),
						"name" => "unionid",
						"disabled"  => true
						));

						$fieldset->addField("refresh_token", "text", array(
						"label" => Mage::helper("weixinlogin")->__("Refresh Token"),
						"name" => "refresh_token",
						"disabled"  => true
						));

						$fieldset->addField('headimgurl', 'image', array(
						'label' => Mage::helper('weixinlogin')->__('Headimgurl'),
						'name' => 'headimgurl',
						'note' => '(*.jpg, *.png, *.gif)'
						));

				if (Mage::getSingleton("adminhtml/session")->getSocialloginData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getSocialloginData());
					Mage::getSingleton("adminhtml/session")->setSocialloginData(null);
				}
				elseif(Mage::registry("sociallogin_data")) {
				    $form->setValues(Mage::registry("sociallogin_data")->getData());
				}
				return parent::_prepareForm();
		}
}
