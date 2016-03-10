<?php

class Magegiant_Dailydeal_Adminhtml_DailydealController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('dailydeal/dailydeal')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Deal Manager'), Mage::helper('adminhtml')->__('Deal Manager'));

		return $this;
	}

	public function indexAction()
	{
		$this->_initAction()
			->renderLayout();
	}

	public function editAction()
	{
		$id    = $this->getRequest()->getParam('id');
		$model = Mage::getModel('dailydeal/dailydeal')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
				$model->setData($data);

			Mage::register('dailydeal_data', $model);
			$this->loadLayout();
			$this->_setActiveMenu('dailydeal/dailydeal');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Deal Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Deal News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_edit'))
				->_addLeft($this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Deal does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction()
	{
		$this->_forward('edit');
	}

	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost()) {

			if (isset($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['name'] != '') {
				try {
					/* Starting upload */
					$uploader1 = new Varien_File_Uploader('thumbnail');

					// Any extention would work
					$uploader1->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
					$uploader1->setAllowRenameFiles(false);

					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader1->setFilesDispersion(false);

					// We set media as the upload dir
					$path_thumbnail_image = Mage::getBaseDir('media') . DS;
					$uploader1->save($path_thumbnail_image . 'dailydeal/main', $_FILES['thumbnail']['name']);

				} catch (Exception $e) {
				}

				//this way the name is saved in DB
				$data['thumbnail_image'] = 'dailydeal/main/' . $_FILES['thumbnail']['name'];
			}
			if (isset($data['thumbnail'])) {
				$delete_thumbnail = ($data['thumbnail']);
				if ($delete_thumbnail['delete'] == 1) {
					$data['thumbnail_image'] = null;
				}
			}
			if (isset($data['candidate_product_id']) && $data['candidate_product_id']) {
				$data['product_id'] = $data['candidate_product_id'];
			}

			if (isset($data['product_name']) && $data['product_name'] == '') {
				unset($data['product_name']);
			}
			$data  = $this->_filterDateTime($data, array('start_time', 'close_time'));
			$model = Mage::getModel('dailydeal/dailydeal')->load($this->getRequest()->getParam('id'));

			try {
				$data['start_time'] = date('Y-m-d H:i:s', Mage::getModel('core/date')->gmtTimestamp(strtotime($data['start_time'])));
				$data['close_time'] = date('Y-m-d H:i:s', Mage::getModel('core/date')->gmtTimestamp(strtotime($data['close_time'])));
			} catch (Exception $e) {
			}

			$price              = Mage::getModel('catalog/product')->load($data['product_id'])->getPrice();
			$data['deal_price'] = $price - $data['save'] * $price / 100;
			$data['status']     = $data['status_form'];
			$data['store_id']   = implode(',', $data['stores']);
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			try {
				if ($price >= $data['deal_price']) {
					if (Mage::getModel('core/date')->timestamp($data['start_time']) <= Mage::getModel('core/date')->timestamp($data['close_time'])) {
						$model->save();
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dailydeal')->__('Deal was successfully saved'));
					} else {
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Start time must be smaller than close time!'));
					}
				} else {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Deal Price must be smaller than Product price'));
				}
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));

					return;
				}
				$this->_redirect('*/*/');

				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Unable to find deal to save'));
		$this->_redirect('*/*/');
	}

	public function deleteAction()
	{
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$model = Mage::getModel('dailydeal/dailydeal');
				$model->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deal was successfully deleted'));

				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction()
	{
		$dealIds = $this->getRequest()->getParam('dailydeal');
		if (!is_array($dealIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Deal(s)'));
		} else {
			try {
				$i = 0;
				foreach ($dealIds as $dealId) {
					$deal = Mage::getModel('dailydeal/dailydeal')->load($dealId);
					$deal->delete();
					$i++;
				}
				if ($i > 0) {
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d Deal(s) were successfully deleted', $i));
				} else {
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deals should not be deleted'));
				}
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massStatusAction()
	{
		$dealIds = $this->getRequest()->getParam('dailydeal');
		if (!is_array($dealIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Deal(s)'));
		} else {
			try {
				foreach ($dealIds as $dealId) {
					$deal = Mage::getSingleton('dailydeal/dailydeal')
						->load($dealId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d Deal(s) were successfully updated', count($dealIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function exportCsvAction()
	{
		$fileName = 'dailydeal.csv';
		$content  = $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}

	public function exportXmlAction()
	{
		$fileName = 'dailydeal.xml';
		$content  = $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_grid')->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}

	public function changeproductAction()
	{
		$product_id = $this->getRequest()->getParam('product_id');
		if ($product_id) {
			$product      = Mage::getModel('catalog/product')->load($product_id);
			$product_name = $product->getName();
			$product_name = str_replace('"', '', $product_name);
			$product_name = str_replace("'", '', $product_name);
			$html         = '<input type="hidden" id="newproduct_name" name="newproduct_name" value="' . $product_name . '" >';
			$this->getResponse()->setHeader('Content-type', 'application/x-json');
			$this->getResponse()->setBody($html);
		}
	}

	public function listproductAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlockSingleton('dailydeal/adminhtml_dailydeal_edit_tab_listproduct')
			->setProduct($this->getRequest()->getPost('aproduct', null))
		;

		$this->renderLayout();
	}

	public function listproductGridAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlockSingleton('dailydeal/adminhtml_dailydeal_edit_tab_listproduct')
			->setProduct($this->getRequest()->getPost('aproduct', null))
		;
		$this->renderLayout();
	}

	public function listorderGridAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlock('dailydeal.edit.tab.listorder')
			->setOrder($this->getRequest()->getPost('aorder', null));
		$this->renderLayout();
	}

}
