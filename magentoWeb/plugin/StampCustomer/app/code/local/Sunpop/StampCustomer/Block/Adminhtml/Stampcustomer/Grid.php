<?php

class Sunpop_StampCustomer_Block_Adminhtml_Stampcustomer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("stampcustomerGrid");
				$this->setDefaultSort("a_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("stampcustomer/stampcustomer")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("a_id", array(
				"header" => Mage::helper("stampcustomer")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "a_id",
				));

				$this->addColumn("a_state", array(
				"header" => Mage::helper("stampcustomer")->__("State"),
				"index" => "a_state",
				'type' => 'options',
				'options' => Mage::helper("stampcustomer")->getState(),
				'sortable'  => true,
				));
				$this->addColumn("a_name", array(
				"header" => Mage::helper("stampcustomer")->__("Name"),
				"index" => "a_name",
				'sortable'  => true,
				));
				$this->addColumn("a_company", array(
				"header" => Mage::helper("stampcustomer")->__("Company"),
				"index" => "a_company",
				'sortable'  => true,
				));
				$this->addColumn("a_certtype", array(
				"header" => Mage::helper("stampcustomer")->__("Certtype"),
				"index" => "a_certtype",
				'sortable'  => true,
				));
				$this->addColumn("a_certspec", array(
				"header" => Mage::helper("stampcustomer")->__("Certspec"),
				"index" => "a_certspec",
				'sortable'  => true,
				));
				$this->addColumn("a_certsn", array(
				"header" => Mage::helper("stampcustomer")->__("Certsn"),
				"index" => "a_certsn",
				'sortable'  => true,
				));
				$this->addColumn("a_stampsn", array(
				"header" => Mage::helper("stampcustomer")->__("Stampsn"),
				"index" => "a_stampsn",
				'sortable'  => true,
				));
				$this->addColumn("a_validatesn", array(
				"header" => Mage::helper("stampcustomer")->__("Validatesn"),
				"index" => "a_validatesn",
				'sortable'  => true,
				));
				$this->addColumn('status', array(
					'header'    => Mage::helper('stampcustomer')->__('Status'),
					'index'     => 'status',
					'width'     => '50',
					'type' => 'options',
					'options' => array('0'=>'Disable','1'=>'Enable')
				));
					$this->addColumn('a_expdate', array(
						'header'    => Mage::helper('stampcustomer')->__('Expdate'),
						'index'     => 'a_expdate',
						'type'      => 'date',
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
			$this->setMassactionIdField('a_id');
			$this->getMassactionBlock()->setFormFieldName('a_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_stampcustomer', array(
					 'label'=> Mage::helper('stampcustomer')->__('Remove'),
					 'url'  => $this->getUrl('adminhtml/stampcustomer/massRemove'),
					 'confirm' => Mage::helper('stampcustomer')->__('Are you sure?')
				));

			$statuses = Mage::helper('stampcustomer')->getOptionArray();
			array_unshift($statuses, array('label'=>'', 'value'=>''));
			$this->getMassactionBlock()->addItem('update_status', array(
				'label'         => Mage::helper('stampcustomer')->__('Update Status'),
				'url'           => $this->getUrl('adminhtml/stampcustomer/massUpdateStatus'),
				'additional'    => array(
					'status'    => array(
						'name'      => 'status',
						'type'      => 'select',
						'class'     => 'required-entry',
						'label'     => Mage::helper('stampcustomer')->__('Status'),
						'values'    => $statuses
					)
				)
			));
			return $this;
		}


}
