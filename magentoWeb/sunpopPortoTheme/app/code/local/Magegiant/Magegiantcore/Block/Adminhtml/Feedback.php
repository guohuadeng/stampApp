<?php
class Magegiant_Magegiantcore_Block_Adminhtml_Feedback extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_feedback';
    $this->_blockGroup = 'magegiantcore';
    $this->_headerText = Mage::helper('magegiantcore')->__('Feedbacks Manager');
    parent::__construct();
    $this->_updateButton('add', 'label', Mage::helper('magegiantcore')->__('Post Feedback'));
  }
}