<?php
class Magehit_Deleteorder_Model_Observer
{
   public function core_block_abstract_to_html_before(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('deleteorder')->isEnabled()) return;

        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction && $block->getRequest()->getControllerName() == 'sales_order') {
            $block->addItem('delete-order-magehit', array(
                'label' => '' . Mage::helper('deleteorder')->__('Delete Order(s)'),
                'url'   => Mage::helper("adminhtml")->getUrl('adminhtml/deleteorder_deleteorders/deleteOrders'),
            ));
        }
    }
}