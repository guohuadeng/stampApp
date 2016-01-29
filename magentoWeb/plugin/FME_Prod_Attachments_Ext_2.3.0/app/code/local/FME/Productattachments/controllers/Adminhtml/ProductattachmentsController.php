<?php

/**
 * Productattachments extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Productattachments
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 � free-magentoextensions.com All right reserved
 */
class FME_Productattachments_Adminhtml_ProductattachmentsController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('productattachments/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Attachment Manager'), Mage::helper('adminhtml')->__('Attachment Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    protected function _initProductAttachments() {


        $productattachments = Mage::getModel('productattachments/productattachments');
        $attachmentId = (int) $this->getRequest()->getParam('id');

        if ($attachmentId) {
            $productattachments->load($attachmentId);
        }
        Mage::register('current_attachment_products', $productattachments);
        return $productattachments;
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('productattachments/productattachments')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('productattachments_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('productattachments/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Attachment Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('productattachments/adminhtml_productattachments_edit'))
                    ->_addLeft($this->getLayout()->createBlock('productattachments/adminhtml_productattachments_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productattachments')->__('File does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            //echo '<pre>';print_r($data);exit;
            //Upload File 
            $files = $this->uploadFiles($_FILES);
            if ($files && is_array($files)) {
                for ($f = 0; $f < count($files); $f++) {
                    if ($files[$f]) {
                        $fieldname = str_replace('_uploader', '', $files[$f]['fieldname']);
                        if (array_key_exists($fieldname, $data)) {
                            $data['filename'] = $files[$f]['url'];

                            //Get File Size, Icon, Type
                            $fileconfig = Mage::getModel('productattachments/image_fileicon');
                            $filePath = Mage::getBaseDir('media') . DS . $data['filename'];
                            $fileconfig->Fileicon($filePath);

                            $data['file_icon'] = $fileconfig->displayIcon();
                            $data['file_type'] = $fileconfig->getType();
                            $data['file_size'] = $fileconfig->getSize();

                            $fileURL = Mage::getBaseUrl('media') . $data['filename'];
                            $data['download_link'] = "<a href='" . $fileURL . "' target='_blank'>Download</a>";
                        }
                    }
                }
            }

            //Save CMS Pages
            if (isset($data['cmspage_id'])) {
				$data['cmspage_id'] = implode(',', $data['cmspage_id']);
			}

            $model = Mage::getModel('productattachments/productattachments');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productattachments')->__('File was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productattachments')->__('Unable to find File to save'));
        $this->_redirect('*/*/');
    }

    /**
     * Get related products grid and serializer block
     */
    public function productsAction() {
        $this->_initProductAttachments();
        $this->loadLayout();
        $this->getLayout()->getBlock('productattachments.edit.tab.products')
                ->setProductsRelatedAttachments($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }

    /**
     * Get related products grid
     */
    public function productsGridAction() {
        $this->_initProductAttachments();
        //Push Existing Values in Array
        $productsarray = array();
        $Id = (int) $this->getRequest()->getParam('id');
        foreach (Mage::registry('current_attachment_products')->getRelatedProducts($Id) as $products) {
            $productsarray = $products["product_id"];
        }

        if (!isset($_POST["products_related"])) {
            $_POST["products_related"] = array();
        }

        array_push($_POST["products_related"], $productsarray);
        Mage::registry('current_attachment_products')->setProductsRelatedAttachments($productsarray);

        $this->loadLayout();
        $this->getLayout()->getBlock('productattachments.edit.tab.products')
                ->setProductsRelatedAttachments($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('productattachments/productattachments');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('File was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $productattachmentsIds = $this->getRequest()->getParam('productattachments');
        if (!is_array($productattachmentsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select File(s)'));
        } else {
            try {
                foreach ($productattachmentsIds as $productattachmentsId) {
                    $productattachments = Mage::getModel('productattachments/productattachments')->load($productattachmentsId);
                    $productattachments->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($productattachmentsIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $productattachmentsIds = $this->getRequest()->getParam('productattachments');
        if (!is_array($productattachmentsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($productattachmentsIds as $productattachmentsId) {
                    $productattachments = Mage::getSingleton('productattachments/productattachments')
                            ->load($productattachmentsId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($productattachmentsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'productattachments.csv';
        $content = $this->getLayout()->createBlock('productattachments/adminhtml_productattachments_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'productattachments.xml';
        $content = $this->getLayout()->createBlock('productattachments/adminhtml_productattachments_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    protected function uploadFiles($files) {
        if (!empty($files) && is_array($files)) {
            $result = array();
            foreach ($files as $file => $info) {
                $result[] = $this->uploadFile($file);
            }
            return $result;
        }
    }

    protected function uploadFile($file_name) {

        if (!empty($_FILES[$file_name]['name'])) {
            $result = array();
            $dynamicScmsURL = 'productattachments' . DS . 'files';
            $baseScmsMediaURL = Mage::getBaseUrl('media') . DS . 'productattachments' . DS . 'files';
            $baseScmsMediaPath = Mage::getBaseDir('media') . DS . 'productattachments' . DS . 'files';

            //Mage::helper('adminhtml')->getAllowedFileExtensions();

            $uploader = new Varien_File_Uploader($file_name);
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'xls', 'xlsx', 'doc', 'docx', 'zip', 'ppt', 'pptx', 'flv', 'mp3', 'mp4', 'csv', 'html', 'bmp', 'txt', 'rtf', 'psd'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save($baseScmsMediaPath);

            $file = str_replace(DS, '/', $result['file']);
            if (substr($baseScmsMediaURL, strlen($baseScmsMediaURL) - 1) == '/' && substr($file, 0, 1) == '/')
                $file = substr($file, 1);

            $ScmsMediaUrl = $dynamicScmsURL . $file;

            $result['fieldname'] = $file_name;
            $result['url'] = $ScmsMediaUrl;
            $result['file'] = $result['file'];
            return $result;
        } else {
            return false;
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}
