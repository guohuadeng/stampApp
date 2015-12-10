<?php
class Sunpop_StampCustomer_Block_Adminhtml_Stampcustomer_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("stampcustomer_form", array("legend"=>Mage::helper("stampcustomer")->__("Item information")));

				
						$fieldset->addField("a_state", "select", array(
						"label" => Mage::helper("stampcustomer")->__("State"),
						"name" => "a_state",
						'required'  => true, 
						'value'     => Mage::registry("stampcustomer_data")->getAState(),
						'values'    => Mage::helper("stampcustomer")->getState(),
						));
					
						$fieldset->addField("a_name", "text", array(
						"label" => Mage::helper("stampcustomer")->__("Name"),
						"name" => "a_name",
						'required'  => true,
						));
					
						$fieldset->addField("a_company", "text", array(
						"label" => Mage::helper("stampcustomer")->__("Company"),
						"name" => "a_company",
						'required'  => true,
						));
					
						$fieldset->addField("a_certtype", "text", array(
						"label" => Mage::helper("stampcustomer")->__("Certtype"),
						"name" => "a_certtype",
						'required'  => true,
						));
					
						$fieldset->addField("a_certsn", "text", array(
						"label" => Mage::helper("stampcustomer")->__("Certsn"),
						"name" => "a_certsn",
						'required'  => true,
						));
					
						$fieldset->addField("a_stampsn", "text", array(
						"label" => Mage::helper("stampcustomer")->__("Stampsn"),
						"name" => "a_stampsn",
						'required'  => true,
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateFormatWithLongYear(
							Mage_Core_Model_Locale::FORMAT_TYPE_FULL
						);
						$fieldset->addField('status', 'select', array(
							'name'      => 'status',
							'label'     => Mage::helper('stampcustomer')->__('Status'),
							'title'     => Mage::helper('stampcustomer')->__('Status'),
							'required'  => true,
							'value'     => Mage::registry("stampcustomer_data")->getStatus(),
							'values'    => array('1'=>Mage::helper('stampcustomer')->__('Enable'),'0'=>Mage::helper('stampcustomer')->__('Disable')),
						));
						$fieldset->addField('a_expdate', 'date', array(
						'label'        => Mage::helper('stampcustomer')->__('expdate'),
						'name'         => 'a_expdate',
						//'time' => true,
						'required'  => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));

				if (Mage::getSingleton("adminhtml/session")->getStampcustomerData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getStampcustomerData());
					Mage::getSingleton("adminhtml/session")->setStampcustomerData(null);
				} 
				elseif(Mage::registry("stampcustomer_data")) {
				    $form->setValues(Mage::registry("stampcustomer_data")->getData());
				}
				return parent::_prepareForm();
		}
}
