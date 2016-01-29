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

class FME_Productattachments_Block_Adminhtml_Productattachments_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'productattachments';
        $this->_controller = 'adminhtml_productattachments';
        
        $this->_updateButton('save', 'label', Mage::helper('productattachments')->__('Save File'));
        $this->_updateButton('delete', 'label', Mage::helper('productattachments')->__('Delete File'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productattachments_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'productattachments_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'productattachments_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('productattachments_data') && Mage::registry('productattachments_data')->getId() ) {
            return Mage::helper('productattachments')->__("Edit File '%s'", $this->htmlEscape(Mage::registry('productattachments_data')->getTitle()));
        } else {
            return Mage::helper('productattachments')->__('Add File');
        }
    }
}