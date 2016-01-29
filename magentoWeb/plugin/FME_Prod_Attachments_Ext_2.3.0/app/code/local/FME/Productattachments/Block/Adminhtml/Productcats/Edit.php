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

class FME_Productattachments_Block_Adminhtml_Productcats_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {

        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'productattachments';
        $this->_controller = 'adminhtml_productcats';

        $this->_updateButton('save', 'label', $this->__('Save Category'));
        $this->_updateButton('delete', 'label', $this->__('Delete Category'));

        $this->_addButton('saveandcontinue', array(
            'label'     => $this->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productattachments_productcats') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'productattachments_productcats');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'productattachments_productcats');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        $data = Mage::registry('productattachments_productcats');
        if( isset($data['category_name'])
        &&  $data['category_name']
        )   return $this->__('Edit Category \'%s\'', $this->htmlEscape($data['category_name']));
        else return $this->__('Add Category');
    }

}