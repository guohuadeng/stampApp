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
 
class FME_Productattachments_Block_Adminhtml_Productattachments_Edit_Tab_Cmspages extends Mage_Adminhtml_Block_Widget_Form
{
    
    protected function _prepareForm()
    {
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('productattachments_form', array('legend'=>Mage::helper('productattachments')->__('Attach With CMS Pages')));
			
		$fieldset->addField('cmspage_id','multiselect',array(
			'name'      => 'cmspage_id',
            'label'     => Mage::helper('productattachments')->__('CMS Pages'),
            'title'     => Mage::helper('productattachments')->__('CMS Pages'),
            'required'  => false,
	    	'values'    => Mage::getModel('productattachments/productattachments')->getCMSPage()
	  	));
		
		$data = Mage::registry('productattachments_data');		
	 	$form->setValues($data);
        return parent::_prepareForm();
        
    }
    
  
}
