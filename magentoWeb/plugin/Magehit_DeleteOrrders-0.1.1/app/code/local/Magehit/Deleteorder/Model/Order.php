<?php
class Magehit_Deleteorder_Model_Order extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        parent::__construct();
    }

    public function deleteOrder($orderIds = array())
    {

        $this->deteleRelated($orderIds);

        if (version_compare(Mage::getVersion(), '1.3.0', '<=') && $this->_deleteOld($orderIds)) {
            return true;
        } else {
            if ($this->_delete($orderIds)) return true;
        }

        return false;

    }
    
    public function _deleteOld($orderIds = array())
    {

        $orderIds             = '(' . implode(",", $orderIds) . ')';
        $resource             = Mage::getSingleton('core/resource');
        $write                = $resource->getConnection('core_write');
        $saleOrder   = $resource->getTableName('sales_order');
        $saleOrderEntity  = $resource->getTableName('sales_order_entity');
        $saleOrderEntityInt = $resource->getTableName('sales_order_entity_int');
        $eavAttr  = $resource->getTableName('eav_attribute');
        $saleFlatOrderItem = $resource->getTableName('sales_flat_order_item');


        try {
            $sql = "DELETE FROM " . $saleOrder . " WHERE entity_id IN " . $orderIds . ";";
            $write->query($sql);
            $sql = "DELETE FROM  " . $saleOrderEntity . " WHERE parent_id IN " . $orderIds . ";";
            $write->query($sql);
            $sql = "DELETE s FROM  " . $saleOrderEntity . " s
                     JOIN  " . $saleOrderEntityInt . " si on s.entity_id = si.entity_id
                     JOIN  " . $eavAttr . " a on si.attribute_id = a.attribute_id
                     WHERE a.attribute_code = 'order_id'
                     AND si.value IN " . $orderIds . ";";
            $write->query($sql);
            $sql = "DELETE FROM  " . $saleFlatOrderItem . " WHERE order_id IN " . $orderIds . ";";
            $write->query($sql);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        return true;
    }

    public function _delete($orderIds = array())
    {

        $orderIds             = '(' . implode(",", $orderIds) . ')';
        $resource             = Mage::getSingleton('core/resource');
        $write                = $resource->getConnection('core_write');
        $saleFlatOrder  = $resource->getTableName('sales_flat_order');
        $saleFlatOrderGrid = $resource->getTableName('sales_flat_order_grid');

        try {
            $sql = "DELETE FROM " . $saleFlatOrder . " WHERE entity_id IN " . $orderIds . ";";
            $write->query($sql);
            $sql = "DELETE FROM  " . $saleFlatOrderGrid . " WHERE entity_id IN " . $orderIds . ";";
            $write->query($sql);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        return true;
    }

    public function deteleRelated($orderIds = array())
    {

        try {
            $invoices = Mage::getResourceModel('sales/order_invoice_collection')->addAttributeToSelect('*')->addFieldToFilter('order_id', array('in', $orderIds));
            foreach ($invoices as $invoice) $invoice->delete();

            $gridInvoices = Mage::getResourceModel('sales/order_invoice_grid_collection')->addFieldToFilter('order_id', array('in', $orderIds));
            foreach ($gridInvoices as $invoice) $invoice->delete();

            $shipments = Mage::getResourceModel('sales/order_shipment_collection')->addAttributeToSelect('*')->addFieldToFilter('order_id', array('in', $orderIds));
            foreach ($shipments as $shipment) $shipment->delete();

            $gridShipments = Mage::getResourceModel('sales/order_shipment_grid_collection')->addAttributeToSelect('*')->addFieldToFilter('order_id', array('in', $orderIds));
            foreach ($gridShipments as $shipment) $shipment->delete();

            $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')->addAttributeToSelect('*')->addFieldToFilter('order_id', array('in', $orderIds));
            foreach ($creditmemos as $creditmemo) $creditmemo->delete();

            $gridCreditMemos = Mage::getResourceModel('sales/order_creditmemo_grid_collection')->addAttributeToSelect('*')->addFieldToFilter('order_id', array('in', $orderIds));
            foreach ($gridCreditMemos as $creditmemo) $creditmemo->delete();

            $transactions = Mage::getResourceModel('sales/order_payment_transaction_collection')->addAttributeToSelect('*')->addFieldToFilter('order_id', array('in', $orderIds));
            foreach ($transactions as $transaction) $transaction->delete();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

}