<?php
class Sunpop_RestConnect_OrderController extends Mage_Core_Controller_Front_Action {

	protected $_attributesMap = array(
			'order' => array('order_id' => 'entity_id'),
			'order_address' => array('address_id' => 'entity_id'),
			'order_payment' => array('payment_id' => 'entity_id'),
			'shipment' =>  array('shipment_id' => 'entity_id'),
			'invoice' => array('invoice_id' => 'entity_id'),
			'invoice_item' => array('item_id' => 'entity_id'),
			'invoice_comment' => array('comment_id' => 'entity_id')
		);

	protected $_ignoredAttributeCodes = array(
        'global'    =>  array('entity_id', 'attribute_set_id', 'entity_type_id')
		);
	public function listAction(){

		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
		   echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'customer_not_login'
			));
			return ;
		}
		$customer_id = $customer->getId();

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
			$orderCollection->addFieldToFilter('customer_id',$customer_id);
			$orderCollection->setOrder('created_at','desc');

			$page = $this->getRequest ()->getParam ( 'page' ) ? $this->getRequest ()->getParam ( 'page' ) : 1;
			$limit = $this->getRequest ()->getParam ( 'limit' ) ? $this->getRequest ()->getParam ( 'limit' ) : 10;
			$orderCollection->setPageSize($limit)->setCurPage($page);
		} catch (Mage_Core_Exception $e) {
			echo json_encode ( array (
					'status' => '0x0002',
					'message' => $e->getMessage()
				));
				return ;
		}
		foreach ($orderCollection as $order) {

			$data = $this->_getAttributes($order, 'order');
			$shipment = $order->getShipmentsCollection()->getFirstItem();
			$data['shipment_increment_id'] = $shipmentIncrementId = $shipment->getIncrementId();
			$data['invoice_increment_id'] = null;
			if ($order->hasInvoices()) {
				$invIncrementIDs = array();
				foreach ($order->getInvoiceCollection() as $inv) {
					$data['invoice_increment_id'] = $inv->getIncrementId();
				}
			}
			$productname = array();
			foreach ($order->getAllItems() as $i=>$item){
				$productid = $item->getProduct()->getId();
				$_product = Mage::getModel('catalog/product')->load($productid);
				//print_r($_product->getData());exit;
				$productname[$i]['name']= $item->getName();
				$productname[$i]['price']= $item->getPrice();

				if(($_product->getImage() == 'no_selection') || (!$_product->getImage())){
					$productname[$i]['image_url']= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/images/catalog/product/placeholder/image.jpg';
				    $productname[$i]['image_thumbnail_url']=  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/images/catalog/product/placeholder/image.jpg';
				}else{
					$productname[$i]['image_url']= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $_product->getImage();
					$productname[$i]['image_thumbnail_url'] = Mage::getModel ( 'catalog/product_media_config' )->getMediaUrl( $_product->getThumbnail() );
				}
				$optionsall = $item->getProductOptions();
				$options = $optionsall['options'];
				$newoptions = array();
				foreach($options as $j=>$option){
					foreach($option as $index=>$p){
						$newoptions[$j][$index] = urlencode($p);
					}
				}
				$productname[$i]['options'] = $newoptions;
			}
			$data['products'] = $productname;
			$orders[] = $data;


		}
		$result = Mage::helper('core')->jsonEncode($orders);
		$this->getResponse()->setBody(urldecode($result));
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

		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
		   echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'customer_not_login'
			));
			return ;
		}
		$customer_id = $customer->getId();

		$increment_id = $this->getRequest()->getParam('increment_id');
		$order = $this->_initOrder($increment_id);
		$order_customer_id = $order->getCustomerId();
		if($customer_id!=$order_customer_id){
			echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'Order increment_id Error'
			));
			return ;
		}


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

			$productinfos = $this->_getAttributes($item, 'order_item');
			$product_options = $productinfos['product_options'];
			$product_optionsarray = unserialize($product_options);
			$options = $product_optionsarray['options'];
			$newoptions = array();
			foreach($options as $j=>$option){
				foreach($option as $index=>$p){
					$newoptions[$j][$index] = urlencode($p);
				}
			}
			$productinfos['product_options'] = $newoptions;
            $result['items'][] = $productinfos;
        }

        $result['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');

        $result['status_history'] = array();

        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }
		$shipment = $order->getShipmentsCollection()->getFirstItem();
		$result['shipment_increment_id'] = $shipmentIncrementId = $shipment->getIncrementId();
		$result['invoice_increment_id'] = '';
		if ($order->hasInvoices()) {
			$invIncrementIDs = array();
			foreach ($order->getInvoiceCollection() as $inv) {
				$result['invoice_increment_id'] = $inv->getIncrementId();
			}
		}

		$result = Mage::helper('core')->jsonEncode($result);
		$this->getResponse()->setBody(urldecode($result));
		//echo json_encode($result);
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
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
		   echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'customer_not_login'
			));
			return ;
		}
		$customer_id = $customer->getId();
		$shipments = array();
        //TODO: add full name logic
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->addAttributeToSelect('*')
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
			$shipmentCollection->addFieldToFilter('customer_id',$customer_id);
			$shipmentCollection->setOrder('created_at','desc');
        } catch (Mage_Core_Exception $e) {
			echo json_encode ( array (
					'code' => '0x0002',
					'message' => $e->getMessage()
			));
			return ;
        }
        foreach ($shipmentCollection as $shipment) {
			$ship = $this->_getAttributes($shipment, 'shipment');
			$order = $shipment->getOrder();
			$ship['order_increment_id'] = $order->getIncrementId();

			$productname = array();
			foreach ($order->getAllItems() as $i=>$item){
				$productid = $item->getProduct()->getId();
				$_product = Mage::getModel('catalog/product')->load($productid);
				//print_r($_product->getData());exit;
				$productname[$i]['name']= $item->getName();
				$productname[$i]['price']= $item->getPrice();

				if(($_product->getImage() == 'no_selection') || (!$_product->getImage())){
					$productname[$i]['image_url']= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/images/catalog/product/placeholder/image.jpg';
				  $productname[$i]['image_thumbnail_url']=  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/images/catalog/product/placeholder/image.jpg';
				}else{
					$productname[$i]['image_url']= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $_product->getImage();
					$productname[$i]['image_thumbnail_url'] = Mage::getModel ( 'catalog/product_media_config' )->getMediaUrl( $_product->getThumbnail() );
				}
			}
			$ship['products'] = $productname;

            $shipments[] = $ship;
        }
		//print_r($shipments);
		echo json_encode($shipments);
	}
	public function shipmentInfoAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
		   echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'customer_not_login'
			));
			return ;
		}
		$customer_id = $customer->getId();
		$shipmentIncrementId = $this->getRequest()->getParam("shipment_increment_id");
		$shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);
		$shipment_customer_id = $shipment->getCustomerId();
		if($shipment_customer_id!=$customer_id){
			echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'shipment increment_id Error'
			));
			return ;
		}
        /* @var $shipment Mage_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
			echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'not_exists'
			));
			return ;
        }

        $result = $this->_getAttributes($shipment, 'shipment');
		$order = $shipment->getOrder();
		$result['order_increment_id'] = $order->getIncrementId();
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
		echo json_encode($result);
	}
	/*
	* filters param string increment_id ,string created_at ,string order_currency_code ,string order_id,string state(Order state),string grand_total(Grand total amount invoiced) ,string invoice_id
	*
	*
	*/

	public function invoiceListAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
		   echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'customer_not_login'
			));
			return ;
		}
		$customer_id = $customer->getId();

		$invoices = array();
        /** @var $invoiceCollection Mage_Sales_Model_Mysql4_Order_Invoice_Collection */
        $invoiceCollection = Mage::getResourceModel('sales/order_invoice_collection');
        $invoiceCollection->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('*');
		$filters = $this->getRequest()->getParams();
        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        try {
            $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['invoice']);
            foreach ($filters as $field => $value) {
                $invoiceCollection->addFieldToFilter($field, $value);
            }
			$invoiceCollection->setOrder('created_at','desc');
        } catch (Mage_Core_Exception $e) {
			echo json_encode ( array (
					'code' => '0x0002',
					'message' => $e->getMessage()
			));
			return ;
        }
        foreach ($invoiceCollection as $invoice) {
			$invoi = $this->_getAttributes($invoice, 'invoice');
			$invoi['order_increment_id'] = $invoice->getOrderIncrementId();
			$orderid = $invoice->getOrderId();
			$order = Mage::getModel('sales/order')->load($orderid);
			if($order->getCustomerId() == $customer_id){
				$productname = array();
				foreach ($order->getAllItems() as $i=>$item){
					$productid = $item->getProduct()->getId();
					$_product = Mage::getModel('catalog/product')->load($productid);
					//print_r($_product->getData());exit;
					$productname[$i]['name']= $item->getName();
					$productname[$i]['price']= $item->getPrice();


				if(($_product->getImage() == 'no_selection') || (!$_product->getImage())){
					$productname[$i]['image_url']= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/images/catalog/product/placeholder/image.jpg';
				  $productname[$i]['image_thumbnail_url']=  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/images/catalog/product/placeholder/image.jpg';
				}else{
					$productname[$i]['image_url']= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $_product->getImage();
					$productname[$i]['image_thumbnail_url'] = Mage::getModel ( 'catalog/product_media_config' )->getMediaUrl( $_product->getThumbnail() );
				}
				}
				$invoi['products'] = $productname;
				$invoices[] = $invoi;
			}
        }
		echo json_encode($invoices);
	}
	/*
	* @param int invoice_increment_id
	*/
	public function invoiceInfoAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
		   echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'customer_not_login'
			));
			return ;
		}
		$customer_id = $customer->getId();


		$invoiceIncrementId = $this->getRequest()->getParam("invoice_increment_id");
		$invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceIncrementId);

		$orderid = $invoice->getOrderId();
		$order = Mage::getModel('sales/order')->load($orderid);
		$invoice_customer_id = $order->getCustomerId();
		if($invoice_customer_id!=$customer_id){
			echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'invoice increment_id Error'
			));
			return ;
		}


        /* @var Mage_Sales_Model_Order_Invoice $invoice */

        if (!$invoice->getId()) {
			echo json_encode ( array (
					'code' => '0x0002',
					'message' => 'not_exists'
			));
			return ;
        }

        $result = $this->_getAttributes($invoice, 'invoice');
        $result['order_increment_id'] = $invoice->getOrderIncrementId();

        $result['items'] = array();
        foreach ($invoice->getAllItems() as $item) {
            $result['items'][] = $this->_getAttributes($item, 'invoice_item');
        }

        $result['comments'] = array();
        foreach ($invoice->getCommentsCollection() as $comment) {
            $result['comments'][] = $this->_getAttributes($comment, 'invoice_comment');
        }
		echo json_encode($result);
	}
}
?>
