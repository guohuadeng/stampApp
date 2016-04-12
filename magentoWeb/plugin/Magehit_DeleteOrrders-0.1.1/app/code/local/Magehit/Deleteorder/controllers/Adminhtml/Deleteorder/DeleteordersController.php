<?php
class Magehit_Deleteorder_Adminhtml_Deleteorder_DeleteordersController extends Mage_Adminhtml_Controller_Action
{
    public function deleteOrdersAction()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');
        if (empty($orderIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('There is no order to process'));
            $this->_redirect('adminhtml/sales_order');
            return;
        }
        try {
            $count = 0;
            foreach($orderIds as $orderId){
                if(Mage::getModel('deleteorder/order')->deleteOrder(array($orderId))) $count++;
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('%s order(s) successfully deleted', $count)
            );
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        $this->_redirect('adminhtml/sales_order');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('deleteorder');
    }
}