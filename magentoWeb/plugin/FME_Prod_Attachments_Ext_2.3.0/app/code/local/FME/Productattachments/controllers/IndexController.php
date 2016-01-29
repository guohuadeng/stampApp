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
class FME_Productattachments_IndexController extends Mage_Core_Controller_Front_Action {

    public function preDispatch() {
        parent::preDispatch();

        $login_before_download = Mage::getStoreConfig('productattachments/general/login_before_download');
        //Productattachments id to check if there is 
        $pid = $this->getRequest()->getParam('id');
        $model = Mage::getModel('productattachments/productattachments')->load($pid);
        $customer_group_id = $model['customer_group_id'];

        //over riding the configuration settings
        if ($customer_group_id == 0) {

            $login_before_download = 0;
        }

        //Checking Configuration settings to see user authentication
        if ($login_before_download) {

            Mage::getSingleton('customer/session')->addError(Mage::helper('productattachments')->__('Please login to download the attachment.'));
            if (!Mage::getSingleton('customer/session')->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }

    public function indexAction() {

        $this->loadLayout();
        $this->renderLayout();
    }

    public function downloadAction() {

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('productattachments/productattachments')->load($id);

        //Checking Customer Group to download the attachment
        $customer_group_id = $model['customer_group_id'];
        $groupid = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if ($customer_group_id == "" || $customer_group_id == null || $customer_group_id == 0) {
            
        } else {
            if ($customer_group_id != $groupid) {
                $cgroup = Mage::getModel('customer/group')->load($customer_group_id);
                $groupName = $cgroup->getCode();

                Mage::getSingleton('customer/session')->addError(Mage::helper('productattachments')->__('This attachment is for only ' . $groupName . ' User Group to download'));

                Mage::app()->getFrontController()
                        ->getResponse()
                        ->setRedirect(Mage::getUrl('customer/account'));
                return;
            }
        }
        //Update Download Counter
        Mage::getModel('productattachments/productattachments')->updateCounter($id);

        $fileconfig = Mage::getModel('productattachments/image_fileicon');
        $filePath = Mage::getBaseDir('media') . DS . $model['filename'];
        $fileconfig->Fileicon($filePath);
        $fileName = $model['filename'];

        $fileType = $fileconfig->getType();
        $fileSize = $fileconfig->getSize();

        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
            ini_set('zlib.output_compression', 'Off');
        }
        header("Content-Type: $fileType");
        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Transfer-Encoding: binary");
        header("Content-length: " . filesize($filePath));
        // read file
        readfile($filePath);
        exit();
    }
    
    public function listAction() {
        
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function viewAction() {
		
		$block = new FME_Productattachments_Block_ListAttachments();
		Mage::register('fme_pa_cat', $block->view());
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function attachmentsAction() {
		
		$block = new FME_Productattachments_Block_ListAttachments();
		Mage::register('fme_pa_att', $block->attachments());
		
		$this->loadLayout();
		$this->renderLayout();
	}
}
