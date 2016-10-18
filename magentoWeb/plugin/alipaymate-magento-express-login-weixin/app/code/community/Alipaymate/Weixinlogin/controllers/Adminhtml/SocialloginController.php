<?php

class Alipaymate_Weixinlogin_Adminhtml_SocialloginController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("weixinlogin/sociallogin")->_addBreadcrumb(Mage::helper("adminhtml")->__("Sociallogin  Manager"),Mage::helper("adminhtml")->__("Sociallogin Manager"));
				return $this;
		}
		public function indexAction()
		{
			    $this->_title($this->__("Weixinlogin"));
			    $this->_title($this->__("Manager Sociallogin"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{
			    $this->_title($this->__("Weixinlogin"));
				$this->_title($this->__("Sociallogin"));
			    $this->_title($this->__("Edit Item"));

				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("weixinlogin/sociallogin")->load($id);
				if ($model->getId()) {
					Mage::register("sociallogin_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("weixinlogin/sociallogin");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Sociallogin Manager"), Mage::helper("adminhtml")->__("Sociallogin Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Sociallogin Description"), Mage::helper("adminhtml")->__("Sociallogin Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("weixinlogin/adminhtml_sociallogin_edit"))->_addLeft($this->getLayout()->createBlock("weixinlogin/adminhtml_sociallogin_edit_tabs"));
					$this->renderLayout();
				}
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("weixinlogin")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Weixinlogin"));
		$this->_title($this->__("Sociallogin"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("weixinlogin/sociallogin")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("sociallogin_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("weixinlogin/sociallogin");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Sociallogin Manager"), Mage::helper("adminhtml")->__("Sociallogin Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Sociallogin Description"), Mage::helper("adminhtml")->__("Sociallogin Description"));

		$this->_addContent($this->getLayout()->createBlock("weixinlogin/adminhtml_sociallogin_edit"))->_addLeft($this->getLayout()->createBlock("weixinlogin/adminhtml_sociallogin_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{
			$post_data=$this->getRequest()->getPost();
				if ($post_data) {
					try {
				 //save image
		try{

if((bool)$post_data['headimgurl']['delete']==1) {

	        $post_data['headimgurl']='';

}
else {

	unset($post_data['headimgurl']);

	if (isset($_FILES)){

		if ($_FILES['headimgurl']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("weixinlogin/sociallogin")->load($this->getRequest()->getParam("id"));
				if($model->getData('headimgurl')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('headimgurl'))));
				}
			}
						$path = Mage::getBaseDir('media') . DS . 'weixinlogin' . DS .'sociallogin'.DS;
						$uploader = new Varien_File_Uploader('headimgurl');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['headimgurl']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['headimgurl']='weixinlogin/sociallogin/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image


						$model = Mage::getModel("weixinlogin/sociallogin")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Sociallogin was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setSocialloginData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					}
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setSocialloginData($this->getRequest()->getPost());
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
						$model = Mage::getModel("weixinlogin/sociallogin");
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
				$ids = $this->getRequest()->getPost('ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("weixinlogin/sociallogin");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}

		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'sociallogin.csv';
			$grid       = $this->getLayout()->createBlock('weixinlogin/adminhtml_sociallogin_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		}
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'sociallogin.xml';
			$grid       = $this->getLayout()->createBlock('weixinlogin/adminhtml_sociallogin_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
