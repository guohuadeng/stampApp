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
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Productattachments_Block_Adminhtml_Productattachments_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('productattachments_form', array('legend'=>Mage::helper('productattachments')->__('File information')));
		
		$fieldset->addField('title', 'text', array(
		  'label'     => Mage::helper('productattachments')->__('Title'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'name'      => 'title',
		));

		$object = Mage::getModel('productattachments/productattachments')->load( $this->getRequest()->getParam('id') );
		$note = false;
		if($object->getFilename()) {
			$File =  Mage::getBaseUrl('media').$object->getFilename();
			
			//Get File Size, Icon, Type
			$fileconfig = Mage::getModel('productattachments/image_fileicon');
			$filePath = Mage::getBaseDir('media'). DS . $object->getFilename();
			$fileconfig->Fileicon($filePath);
			$DownloadURL = $fileconfig->displayIcon().'&nbsp;&nbsp;<a href='.$File.' target="_blank">Download Current File!</a>';
		} else {
			$DownloadURL = '';
		}
				
		$fieldset->addField('my_file_uploader', 'file', array(
			'label'        => Mage::helper('productattachments')->__('File'),
			'note'      => $note,
			'name'        => 'my_file_uploader',
			'class'     => (($object->getFilename()) ? '' : 'required-entry'),
			'required'  => (($object->getFilename()) ? false : true),
			'after_element_html' => $DownloadURL,
		 )); 
				
		$fieldset->addField('my_file', 'hidden', array(
			'name'        => 'my_file',
		));
		
		$fieldset->addField('store_id','multiselect',array(
			'name'      => 'stores[]',
			'label'     => Mage::helper('productattachments')->__('Store View'),
			'title'     => Mage::helper('productattachments')->__('Store View'),
			'required'  => true,
			'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
		));
		
		$fieldset->addField('status', 'select', array(
		  'label'     => Mage::helper('productattachments')->__('Status'),
		  'name'      => 'status',
		  'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('productattachments')->__('Enabled'),
			  ),
		
			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('productattachments')->__('Disabled'),
			  ),
		  ),
		));
		
		$fieldset->addField('content', 'editor', array(
		  'name'      => 'content',
		  'label'     => Mage::helper('productattachments')->__('Content'),
		  'title'     => Mage::helper('productattachments')->__('Content'),
		  'style'     => 'width:400px; height:200px;',
		  'wysiwyg'   => false,
		  'required'  => true,
		));
		
		if ( Mage::getSingleton('adminhtml/session')->getProductattachmentsData() )
		{
		  $form->setValues(Mage::getSingleton('adminhtml/session')->getProductattachmentsData());
		  Mage::getSingleton('adminhtml/session')->setProductattachmentsData(null);
		} elseif ( Mage::registry('productattachments_data') ) {
		  $form->setValues(Mage::registry('productattachments_data')->getData());
		}
		return parent::_prepareForm();
  }
}