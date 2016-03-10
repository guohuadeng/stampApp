<?php

class Magegiant_Magegiantcore_Block_Adminhtml_Feedback_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'magegiantcore';
        $this->_controller = 'adminhtml_feedback';
        
        $this->_updateButton('save', 'label', Mage::helper('magegiantcore')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('magegiantcore')->__('Delete'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		
		if($this->getRequest()->getParam('id')){
			$this->_addButton('sendfeedback', array(
				'label'     => Mage::helper('adminhtml')->__('Resend'),
				'onclick'   => 'location.href=\''.$this->getUrl('*/*/resend',array('id'=>$this->getRequest()->getParam('id'))).'\'',
				'class'     => 'add',
			), -1);		
		}
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('magegiantcore_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'magegiantcore_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'magegiantcore_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('feedback_data') && Mage::registry('feedback_data')->getId() ) {
            return Mage::helper('magegiantcore')->__("Edit Feedback for '%s'", $this->htmlEscape(Mage::registry('feedback_data')->getExtension()));
        }
    }
}