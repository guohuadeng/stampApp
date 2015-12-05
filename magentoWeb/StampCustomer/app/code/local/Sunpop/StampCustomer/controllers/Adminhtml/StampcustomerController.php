<?php

class Sunpop_StampCustomer_Adminhtml_StampcustomerController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("stampcustomer/stampcustomer")->_addBreadcrumb(Mage::helper("adminhtml")->__("Stampcustomer  Manager"),Mage::helper("adminhtml")->__("Stampcustomer Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("StampCustomer"));
			    $this->_title($this->__("Manager Stampcustomer"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("StampCustomer"));
				$this->_title($this->__("Stampcustomer"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("stampcustomer/stampcustomer")->load($id);
				if ($model->getId()) {
					Mage::register("stampcustomer_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("stampcustomer/stampcustomer");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Stampcustomer Manager"), Mage::helper("adminhtml")->__("Stampcustomer Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Stampcustomer Description"), Mage::helper("adminhtml")->__("Stampcustomer Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("stampcustomer/adminhtml_stampcustomer_edit"))->_addLeft($this->getLayout()->createBlock("stampcustomer/adminhtml_stampcustomer_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("stampcustomer")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("StampCustomer"));
		$this->_title($this->__("Stampcustomer"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("stampcustomer/stampcustomer")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("stampcustomer_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("stampcustomer/stampcustomer");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Stampcustomer Manager"), Mage::helper("adminhtml")->__("Stampcustomer Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Stampcustomer Description"), Mage::helper("adminhtml")->__("Stampcustomer Description"));


		$this->_addContent($this->getLayout()->createBlock("stampcustomer/adminhtml_stampcustomer_edit"))->_addLeft($this->getLayout()->createBlock("stampcustomer/adminhtml_stampcustomer_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("stampcustomer/stampcustomer")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Stampcustomer was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setStampcustomerData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setStampcustomerData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("stampcustomer/stampcustomer");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('a_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("stampcustomer/stampcustomer");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
		
		
		public function massUpdateStatusAction(){
			try{
				$ids = $this->getRequest()->getPost('a_ids', array());
				$status = $this->getRequest()->getPost('status');
				foreach($ids as $id){
					$model = Mage::getModel("stampcustomer/stampcustomer")->load($id);
					if($status){
						$model->setStatus(true);
					}else{
						$model->setStatus(false);
					}
					$model->save();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully changed"));
			} catch ( Exception $e ){
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'stampcustomer.csv';
			$grid       = $this->getLayout()->createBlock('stampcustomer/adminhtml_stampcustomer_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'stampcustomer.xml';
			$grid       = $this->getLayout()->createBlock('stampcustomer/adminhtml_stampcustomer_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
