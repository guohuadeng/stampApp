<?php
/**
 * Product Attachments extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Product Attachments
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 **/
class FME_Productattachments_Adminhtml_ProductcatsController extends Mage_Adminhtml_Controller_Action
{
    /*
     * Preparing layout
     */
    protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('productattachments/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Product Attachments Categories'), Mage::helper('adminhtml')->__('Product Attachments Categories'));
		
		return $this;
	}  

    public function indexAction() { $this->_initAction()->renderLayout(); }

    public function newAction() { $this->_forward('edit'); }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $data = Mage::getModel('productattachments/productcats')->load($id)->getData();
        $session = Mage::getSingleton('adminhtml/session');

        if(isset($data['category_id']) || $id == 0)
        {
            $sessionData = $session->getKBaseCategoryData(true);
            $session->setKBaseCategoryData(false);

            if(is_array($sessionData)) $data = array_merge($data, $sessionData);

            // for compatibility with previous KB versions
            if(isset($data['category_url_key']))
                $data['category_url_key'] = urldecode($data['category_url_key']);

            Mage::register('productattachments_productcats', $data);

            $this->loadLayout();
			$this->_setActiveMenu('productattachments/items');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('productattachments/adminhtml_productcats_edit'))
                ->_addLeft($this->getLayout()->createBlock('productattachments/adminhtml_productcats_edit_tabs'));

            $this->renderLayout();
        }
        else
        {
            $session->addError($this->__('Category does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');

        if($data = $this->getRequest()->getPost())
        {
            try
            {
				$path = Mage::getBaseDir('media') . DS . 'productattachments';
				if (isset($_FILES['category_image']['name']) && $_FILES['category_image']['name'] != '') {//echo '<pre>';print_r($_FILES['event_image']);exit;
					try {
						/* Starting upload */
						$uploader = new Varien_File_Uploader('category_image');
						// Any extention would work
						$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
						$uploader->setAllowRenameFiles(false);
						// Set the file upload mode 
						// false -> get the file directly in the specified folder
						// true -> get the file in the product like folders 
						//	(file.jpg will go in something like /media/f/i/file.jpg)
						$uploader->setFilesDispersion(false);
						// We set media as the upload dir

						$uploader->save($path, $_FILES['category_image']['name']);
						$varImg = new Varien_Image($path . DS . $_FILES['category_image']['name']);
						$varImg->constrainOnly(TRUE);
						$varImg->keepAspectRatio(FALSE);
						$varImg->keepFrame(TRUE);
						$varImg->keeptransparency(FALSE);
						$varImg->backgroundColor(array(255, 255, 255)); // WHITE BACKGROUND
						$image_name = $_FILES['category_image']['name'];
						$varImg->resize(400, 400);
						$varImg->save($path, $image_name);
						$data['category_image'] = 'productattachments' . DS . $image_name;
						
					} catch (Exception $e) {
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productattachments')->__('Error: ' . $e->getMessage()));
						Mage::getSingleton('adminhtml/session')->setFormData($data);
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

						return;
					}
					
					
				}
                $id = $this->getRequest()->getParam('id');
                // checking URL key
                if( !isset($data['category_url_key']) ||  !$data['category_url_key'] )   
				$data['category_url_key'] = $data['category_name'];
				

                $data['category_url_key'] = FME_Productattachments_Helper_Data::nameToUrlKey($data['category_url_key']);

                $model = Mage::getModel('productattachments/productcats')
                            ->setData($data)
                            ->setId($id);

                if($model->isUrlKeyUsed())
                {
                    $session->addError($this->__('URL key is not unique within category store views'));
                    $session->setKBaseCategoryData($data);
                    $this->_redirect('*/*/edit', array('id' => $id));
                    return;
                }
				
				$model->save();
				
                $session->addSuccess($this->__('Category was successfully saved'));
                //$session->setKBaseCategoryData(false);

                if($this->getRequest()->getParam('back'))
                {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e)
            {
                Mage::logException($e);
                $session->addError($e->getMessage());
                $session->setKBaseCategoryData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        else
        {
            $session->addError($this->__('Unable to find an category to save'));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        $session = Mage::getSingleton('adminhtml/session');

        if($id = $this->getRequest()->getParam('id'))
        {
            try
            {
                Mage::getModel('productattachments/productcats')->deleteCategory($id);
                $session->addSuccess($this->__('Category was successfully deleted'));
                $this->_redirect('*/*/');
            }
            catch (Exception $e)
            {
                Mage::logException($e);
                $session->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $kbaseIds = $this->getRequest()->getParam('category_ids');

        if(!is_array($kbaseIds)) {
            $session->addError($this->__('Please select category(s)'));
        }
        else
        {
            try
            {
                $model = Mage::getModel('productattachments/productcats');

                foreach($kbaseIds as $kbaseId)
                    Mage::getModel('productattachments/productcats')->deleteCategory($kbaseId);

                $session->addSuccess($this->__('Total of %d category(s) were successfully deleted', count($kbaseIds)));
            }
            catch (Exception $e)
            {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $kbaseIds = $this->getRequest()->getParam('category_ids');
        if(!is_array($kbaseIds)) {
            $session->addError($this->__('Please select item(s)'));
        }
        else
        {
            try
            {
                foreach($kbaseIds as $kbaseId)
				Mage::getModel('productattachments/productcats')->changeStatus($kbaseId,$this->getRequest()->getParam('status'));
                   
                $session->addSuccess($this->__('Total of %d category(s) were successfully updated', count($kbaseIds)));
            }
            catch (Exception $e)
            {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}
