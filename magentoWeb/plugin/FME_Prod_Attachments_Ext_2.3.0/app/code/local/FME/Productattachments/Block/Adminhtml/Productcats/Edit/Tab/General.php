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
 * @copyright  Copyright 2010 � free-magentoextensions.com All right reserved
 * */
class FME_Productattachments_Block_Adminhtml_Productcats_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $id = $this->getRequest()->getParam('id');
        $form = new Varien_Data_Form();
        $_helper = Mage::helper('productattachments');

        $fieldset = $form->addFieldset('general', array(
            'legend' => $this->__('General information'),
        ));

        $fieldset->addField('category_name', 'text', array(
            'name' => 'category_name',
            'label' => $this->__('Title'),
            'class' => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('category_image', 'image', array(
            'label' => Mage::helper('productattachments')->__('Image'),
            'required' => false,
            'name' => 'category_image',
            'note' => Mage::helper('productattachments')->__('<p>(upload image files only)</p>')
        ));

        if ($this->_getCategories(null, true)->count() > 0) {

            $fieldset->addField('parent_category_id', 'select', array(
                'label' => Mage::helper('productattachments')->__('Parent Category'),
                'name' => 'parent_category_id',
                'type' => 'text',
                'values' => $_helper->getCategoryList($id),
            ));
        }

        if (Mage::app()->isSingleStoreMode()) {

            $fieldset->addField('category_store_ids', 'hidden', array(
                'name' => 'category_store_ids[]',
                'value' => Mage::app()->getStore()->getId(),
            ));
        } else {
            $fieldset->addField('category_store_ids', 'multiselect', array(
                'name' => 'category_store_ids[]',
                'label' => $this->__('Store View'),
                'title' => $this->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }

        $fieldset->addField('category_status', 'select', array(
            'name' => 'category_status',
            'label' => $this->__('Status'),
            'values' => FME_Productattachments_Model_Status::getOptionArray(),
        ));

        $data = Mage::registry('productattachments_productcats');
        if (!is_array($data))
            $data = array();

        if (isset($data['category_store_ids'])) {
            if (Mage::app()->isSingleStoreMode()) {
                if (is_array($data['category_store_ids']))
                    $data['category_store_ids'] = isset($data['category_store_ids'][0]) ? $data['category_store_ids'][0] : '';
            }
        }

        $form->setValues($data);
        $this->setForm($form);

        /* if (Mage::getSingleton('adminhtml/session')->getProductattachmentsProductcats()) {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getProductattachmentsProductcats());
          Mage::getSingleton('adminhtml/session')->setgetProductattachmentsProductcats(null);
          } elseif (Mage::registry('productattachments_productcats')) {//
          $form->setValues(Mage::registry('productattachments_productcats')->getData());
          } */

        return parent::_prepareForm();
    }

    protected function _getCategories() {

        $data = Mage::registry('productattachments_productcats');
        $_helper = Mage::helper('productattachments');

        $collection = $_helper->getAllCategories(null, true);

        if (!empty($data) && isset($data['category_id'])) {
            $collection->addFieldToFilter('main_table.category_id', array('neq' => $data['category_id'])); //echo (string) $collection->getSelect();exit;
        }

        return $collection;
    }

}
