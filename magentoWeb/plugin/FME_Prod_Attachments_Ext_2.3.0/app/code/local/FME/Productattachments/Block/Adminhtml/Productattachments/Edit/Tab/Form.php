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
class FME_Productattachments_Block_Adminhtml_Productattachments_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('productattachments_form', array('legend' => Mage::helper('productattachments')->__('File information')));
        $_helper = Mage::helper('productattachments');


        $fieldset->addField('cat_id', 'select', array(
            'label' => Mage::helper('productattachments')->__('Category'),
            'name' => 'cat_id',
            'type' => 'text',
            'values' => Mage::getModel('productattachments/productcats')->getCollection()->toOptionArray(),//Mage::helper('productattachments')->getSelectcat(),
            'required' => true
        ));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('productattachments')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));

        $object = Mage::getModel('productattachments/productattachments')->load($this->getRequest()->getParam('id'));
        $note = false;
        if ($object->getFilename()) {
            $File = Mage::getBaseUrl('media') . $object->getFilename();

            //Get File Size, Icon, Type
            $fileconfig = Mage::getModel('productattachments/image_fileicon');
            $filePath = Mage::getBaseDir('media') . DS . $object->getFilename();
            $fileconfig->Fileicon($filePath);
            $DownloadURL = $object->getFileIcon() . '&nbsp;&nbsp;<a href=' . $File . ' target="_blank">Download Current File!</a>';
        } else {
            $DownloadURL = '';
        }

        $fieldset->addField('my_file_uploader', 'file', array(
            'label' => Mage::helper('productattachments')->__('File'),
            'note' => $note,
            'name' => 'my_file_uploader',
            //'class'     => (($object->getFilename()) ? '' : 'required-entry'),
            //'required'  => (($object->getFilename()) ? false : true),
            'after_element_html' => $DownloadURL,
        ));

        $fieldset->addField('my_file', 'hidden', array(
            'name' => 'my_file',
        ));

        $fieldset->addField('store_id', 'multiselect', array(
            'name' => 'stores[]',
            'label' => Mage::helper('productattachments')->__('Store View'),
            'title' => Mage::helper('productattachments')->__('Store View'),
            'required' => true,
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('productattachments')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('productattachments')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('productattachments')->__('Disabled'),
                ),
            ),
        ));

        
        $fieldset->addField('customer_group_id', 'select', array(
            'label' => Mage::helper('productattachments')->__('Customer Group'),
            'name' => 'customer_group_id',
            'values' => Mage::getModel('customer/group')->getCollection()->toOptionArray(),
            'after_element_html' => '<p class="nm"><small>' . Mage::helper('productattachments')->__('(This option will override the configuration settings)') . '</small></p>'
        ));

        $fieldset->addField('limit_downloads', 'text', array(
            'label' => Mage::helper('productattachments')->__('Limit Downloads'),
            'name' => 'limit_downloads',
            'after_element_html' => '<p class="nm"><small>' . Mage::helper('productattachments')->__('(Enter number of downloads for this attachment. If empty then unlimited.)') . '</small></p>'
        ));

        $fieldset->addField('block_position', 'select', array(
            'label' => Mage::helper('productattachments')->__('Block Position'),
            'name' => 'block_position',
            'values' => array(
                array(
                    'value' => 'additional',
                    'label' => 'Additional Info (Product page only)'
                ),
                array(
                    'value' => 'other',
                    'label' => 'Other (Product page only)'
                ),
            ),
            'disabled' => false,
            'readonly' => false,
            'after_element_html' => Mage::helper('productattachments')->__('<br/><small>(%s)</small>', 'Postion for the attachment to appear'),
                //'tabindex' => 1
        ));

        $fieldset->addField('link_url', 'text', array(
            'label' => Mage::helper('productattachments')->__('URL'),
            'name' => 'link_url',
            'class' => 'validate-url',
            'after_element_html' => Mage::helper('productattachments')->__('<p class="nm"><small>(%s)</small></p>', 'Link will appear')
        ));

        $fieldset->addField('link_title', 'text', array(
            'label' => Mage::helper('productattachments')->__('Link Title'),
            'name' => 'link_title',
            'after_element_html' => Mage::helper('productattachments')->__('<p class="nm"><small>(%s)</small></p>', 'Title for Link')
        ));

        $fieldset->addField('embed_video', 'text', array(
            'label' => Mage::helper('productattachments')->__('Video'),
            'name' => 'embed_video',
            'class' => 'validate-url',
            'after_element_html' => Mage::helper('productattachments')->__('<p class="nm"><small>(%s)</small></p>', 'URL for video.')
        ));

        $fieldset->addField('video_title', 'text', array(
            'label' => Mage::helper('productattachments')->__('Video Title'),
            'name' => 'video_title',
            'after_element_html' => Mage::helper('productattachments')->__('<p class="nm"><small>(%s)</small></p>', 'Title for Video')
        ));


        $fieldset->addField('content', 'editor', array(
            'name' => 'content',
            'label' => Mage::helper('productattachments')->__('Content'),
            'title' => Mage::helper('productattachments')->__('Content'),
            'style' => 'width:400px; height:200px;',
            'wysiwyg' => false,
            'required' => false,
        ));

        if (Mage::getSingleton('adminhtml/session')->getProductattachmentsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getProductattachmentsData());
            Mage::getSingleton('adminhtml/session')->setProductattachmentsData(null);
        } elseif (Mage::registry('productattachments_data')) {
            $form->setValues(Mage::registry('productattachments_data')->getData());
        }
        return parent::_prepareForm();
    }

}
