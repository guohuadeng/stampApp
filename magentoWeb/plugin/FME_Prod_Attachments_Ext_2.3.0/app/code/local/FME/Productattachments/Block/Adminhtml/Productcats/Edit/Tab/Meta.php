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

class FME_Productattachments_Block_Adminhtml_Productcats_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
    
    protected function _prepareForm()
    {
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('meta_fieldset', array('legend' => Mage::helper('productattachments')->__('Meta Data')));
             
    	$fieldset->addField('meta_keywords', 'editor', array(
            'name'		=> 'meta_keywords',
            'label'		=> Mage::helper('productattachments')->__('Keywords'),
            'title'		=> Mage::helper('productattachments')->__('Meta Keywords'),
    		'required'	=> false
        ));

    	$fieldset->addField('meta_description', 'editor', array(
            'name'		=> 'meta_description',
            'label'		=> Mage::helper('productattachments')->__('Description'),
            'title'		=> Mage::helper('productattachments')->__('Meta Description'),
    		'required'	=> false
        ));
        
		$data = Mage::registry('productattachments_productcats');		
	 	$form->setValues($data);
        return parent::_prepareForm();
        
    }
    
  
}