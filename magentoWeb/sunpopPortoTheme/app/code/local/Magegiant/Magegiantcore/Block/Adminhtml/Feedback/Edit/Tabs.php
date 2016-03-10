<?php

class Magegiant_Magegiantcore_Block_Adminhtml_Feedback_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('feedback_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('magegiantcore')->__('Feedback Detail'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('magegiantcore')->__('Feedback Detail'),
          'title'     => Mage::helper('magegiantcore')->__('Feedback Detail'),
          'content'   => $this->getLayout()->createBlock('magegiantcore/adminhtml_feedback_edit_tab_form')->toHtml(),
      ));
     
	if($this->getRequest()->getParam('id')){
		$this->addTab('message_section', array(
          'label'     => Mage::helper('magegiantcore')->__('Post Message'),
          'title'     => Mage::helper('magegiantcore')->__('Post Message'),
          'content'   => $this->getLayout()->createBlock('magegiantcore/adminhtml_feedback_edit_tab_message')->toHtml(),
		));	 
	  
		$this->addTab('history_section', array(
          'label'     => Mage::helper('magegiantcore')->__('View Posted Message'),
          'title'     => Mage::helper('magegiantcore')->__('View Posted Message'),
          'content'   => $this->getLayout()->createBlock('magegiantcore/adminhtml_feedback_edit_tab_history')->toHtml(),
		));	 	  
	}
      return parent::_beforeToHtml();
  }
}