<?php 
	class Sunpop_RestConnect_OrderController extends Mage_Core_Controller_Front_Action {
		
		protected $_attributesMap = array(
				'order' => array('order_id' => 'entity_id'),
				'order_address' => array('address_id' => 'entity_id'),
				'order_payment' => array('payment_id' => 'entity_id'),
				'shipment' =>  array('shipment_id' => 'entity_id')
			);
		
		protected $_ignoredAttributeCodes = array(
        'global'    =>  array('entity_id', 'attribute_set_id', 'entity_type_id')
		);
		public function listAction(){
			 $orders = array();

        //TODO: add full name logic
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';

        /** @var $orderCollection Mage_Sales_Model_Mysql4_Order_Collection */
        $orderCollection = Mage::getModel("sales/order")->getCollection();
        $billingFirstnameField = "$billingAliasName.firstname";
        $billingLastnameField = "$billingAliasName.lastname";
        $shippingFirstnameField = "$shippingAliasName.firstname";
        $shippingLastnameField = "$shippingAliasName.lastname";
        $orderCollection->addAttributeToSelect('*')
            ->addAddressFields()
            ->addExpressionFieldToSelect('billing_firstname', "{{billing_firstname}}",
                array('billing_firstname' => $billingFirstnameField))
            ->addExpressionFieldToSelect('billing_lastname', "{{billing_lastname}}",
                array('billing_lastname' => $billingLastnameField))
            ->addExpressionFieldToSelect('shipping_firstname', "{{shipping_firstname}}",
                array('shipping_firstname' => $shippingFirstnameField))
            ->addExpressionFieldToSelect('shipping_lastname', "{{shipping_lastname}}",
                array('shipping_lastname' => $shippingLastnameField))
            ->addExpressionFieldToSelect('billing_name', "CONCAT({{billing_firstname}}, ' ', {{billing_lastname}})",
                array('billing_firstname' => $billingFirstnameField, 'billing_lastname' => $billingLastnameField))
            ->addExpressionFieldToSelect('shipping_name', 'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})',
                array('shipping_firstname' => $shippingFirstnameField, 'shipping_lastname' => $shippingLastnameField)
        );

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
		$filters = $this->getRequest()->getParams();
        $filters = $apiHelper->parseFilters($filters, $_attributesMap['order']);
        try {
            foreach ($filters as $field => $value) {
                $orderCollection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
			echo json_encode ( array (
					'status' => '0x0002',
					'message' => $e->getMessage()
				));
				return ;
        }
        foreach ($orderCollection as $order) {
            $orders[] = $this->_getAttributes($order, 'order');
        }
		echo json_encode($orders);
	}
	
	protected function _getAttributes($object, $type, array $attributes = null)
    {
        $result = array();

        if (!is_object($object)) {
            return $result;
        }

        foreach ($object->getData() as $attribute=>$value) {
            if ($this->_isAllowedAttribute($attribute, $type, $attributes)) {
                $result[$attribute] = $value;
            }
        }

        if (isset($_attributesMap['global'])) {
            foreach ($_attributesMap['global'] as $alias=>$attributeCode) {
                $result[$alias] = $object->getData($attributeCode);
            }
        }

        if (isset($_attributesMap[$type])) {
            foreach ($_attributesMap[$type] as $alias=>$attributeCode) {
                $result[$alias] = $object->getData($attributeCode);
            }
        }

        return $result;
    }
	
	protected function _isAllowedAttribute($attributeCode, $type, array $attributes = null)
    {
        if (!empty($attributes)
            && !(in_array($attributeCode, $attributes))) {
            return false;
        }

        if (in_array($attributeCode, $this->_ignoredAttributeCodes['global'])) {
            return false;
        }

        if (isset($this->_ignoredAttributeCodes[$type])
            && in_array($attributeCode, $this->_ignoredAttributeCodes[$type])) {
            return false;
        }

        return true;
    }
	
	public function infoAction(){
		$increment_id = $this->getRequest()->getParam('increment_id');
		$order = $this->_initOrder($increment_id);
		
		if ($order->getGiftMessageId() > 0) {
            $order->setGiftMessage(
                Mage::getSingleton('giftmessage/message')->load($order->getGiftMessageId())->getMessage()
            );
        }

        $result = $this->_getAttributes($order, 'order');

        $result['shipping_address'] = $this->_getAttributes($order->getShippingAddress(), 'order_address');
        $result['billing_address']  = $this->_getAttributes($order->getBillingAddress(), 'order_address');
        $result['items'] = array();

        foreach ($order->getAllItems() as $item) {
            if ($item->getGiftMessageId() > 0) {
                $item->setGiftMessage(
                    Mage::getSingleton('giftmessage/message')->load($item->getGiftMessageId())->getMessage()
                );
            }

            $result['items'][] = $this->_getAttributes($item, 'order_item');
        }

        $result['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');

        $result['status_history'] = array();

        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }
		echo json_encode($result);
	}
	
	protected function _initOrder($orderIncrementId)
    {
        $order = Mage::getModel('sales/order');

        /* @var $order Mage_Sales_Model_Order */

        $order->loadByIncrementId($orderIncrementId);

        if (!$order->getId()) {
			echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'not_exists' 
			));
			return ;
        }

        return $order;
    }
	
	public function addCommentAction(){
		$increment_id = $this->getRequest()->getParam('increment_id');
		$status = $this->getRequest()->getParam('status');
		$comment = $this->getRequest()->getParam('comment');
		$order = $this->_initOrder($increment_id);
		
		$historyItem = $order->addStatusHistoryComment($comment, $status);
        $historyItem->setIsCustomerNotified($notify)->save();


        try {
            if ($notify && $comment) {
                $oldStore = Mage::getDesign()->getStore();
                $oldArea = Mage::getDesign()->getArea();
                Mage::getDesign()->setStore($order->getStoreId());
                Mage::getDesign()->setArea('frontend');
            }

            $order->save();
            $order->sendOrderUpdateEmail($notify, $comment);
            if ($notify && $comment) {
                Mage::getDesign()->setStore($oldStore);
                Mage::getDesign()->setArea($oldArea);
            }
			echo json_encode ( array (
					'code' => true,
					'message' => "comment add sucessfully"
			));
			return ;
        } catch (Mage_Core_Exception $e) {
			echo json_encode ( array (
					'code' => '0x0002',
					'message' => $e->getMessage()
			));
			return ;
        }
	}
	
	public function shipmentListAction(){
		$shipments = array();
        //TODO: add full name logic
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('total_qty')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
            ->joinAttribute('order_increment_id', 'order/increment_id', 'order_id', null, 'left')
            ->joinAttribute('order_created_at', 'order/created_at', 'order_id', null, 'left');
        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        try {
			$filters = $this->getRequest()->getParams();
            $filters = $apiHelper->parseFilters($filters, array('shipment_id' => 'entity_id'));
            foreach ($filters as $field => $value) {
                $shipmentCollection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
			echo json_encode ( array (
					'code' => '0x0002',
					'message' => $e->getMessage()
			));
			return ;
        }
        foreach ($shipmentCollection as $shipment) {
            $shipments[] = $this->_getAttributes($shipment, 'shipment');
        }
		echo json_encode($shipments);
	}
	public function shipmentInfoAction(){
		
		$shipmentIncrementId = $this->getRequest()->getParam("shipment_increment_id");
		$shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        /* @var $shipment Mage_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
			echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'not_exists'
			));
			return ;
        }

        $result = $this->_getAttributes($shipment, 'shipment');

        $result['items'] = array();
        foreach ($shipment->getAllItems() as $item) {
            $result['items'][] = $this->_getAttributes($item, 'shipment_item');
        }

        $result['tracks'] = array();
        foreach ($shipment->getAllTracks() as $track) {
            $result['tracks'][] = $this->_getAttributes($track, 'shipment_track');
        }

        $result['comments'] = array();
        foreach ($shipment->getCommentsCollection() as $comment) {
            $result['comments'][] = $this->_getAttributes($comment, 'shipment_comment');
        }
		//echo json_encode($result);
		echo json_encode($result);
	}
}
?>