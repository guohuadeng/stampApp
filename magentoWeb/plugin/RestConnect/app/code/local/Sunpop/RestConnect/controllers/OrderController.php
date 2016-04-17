<?php
/**
 * * NOTICE OF LICENSE
 * * This source file is subject to the Open Software License (OSL 3.0)
 *
 * Author: Ivan Deng
 * QQ: 300883
 * Email: 300883@qq.com
 * @copyright  Copyright (c) 2008-2015 Sunpop Ltd. (http://www.sunpop.cn)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
header("Access-Control-Allow-Origin: *");
header("P3P: CP=CAO PSA OUR");

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

	/* *
	*@param string $orderType  为空获取所有订单 ,notpaid, 未付款 , notshipped 未发货 , notreceived 待收货 ,complete  完成
	*@param int $page ,int $limit,
	*@return josn
	*/
	public function listAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();

		if (!$customer->getId()) {
		   echo json_encode ( array (
		      'status' => false,
					'code' => 2,
					'message' => 'customer_not_login'
			));
			return ;
		}
		$orderlist = array();
		$page = $this->getRequest ()->getParam ( 'page' ) ? $this->getRequest ()->getParam ( 'page' ) : 1;
		$limit = $this->getRequest ()->getParam ( 'limit' ) ? $this->getRequest ()->getParam ( 'limit' ) : 10;
	  $orderType = $this->getRequest ()->getParam ( 'ordertype' ) ? $this->getRequest ()->getParam ( 'ordertype' ) : 'all';
	  $showfile = $this->getRequest ()->getParam ( 'showfile' ) ? $this->getRequest ()->getParam ( 'showfile' ) : 0;
		//所有
    if($orderType == 'all'){
			$orderlist = $this->_getOrderList($customer,'',$page,$limit,$showfile);
		}
		//未付款
		if($orderType == 'notpaid'){
			$orderlist = $this->_getOrderList($customer,'notpaid',$page,$limit,$showfile);
		}
		//未发货
		if($orderType == 'notshipped'){
			$orderlist = $this->_getOrderList($customer,'notshipped',$page,$limit,$showfile);
		}
		//待收货
		if($orderType == 'notreceived'){
			$orderlist = $this->_getOrderList($customer,'notreceived',$page,$limit,$showfile);
		}
		//已完成
		if($orderType == 'complete'){
			$orderlist = $this->_getOrderList($customer,'complete',$page,$limit,$showfile);
		}
		$result = Mage::helper('core')->jsonEncode($orderlist);
		$this->getResponse()->setBody(urldecode($result));
	}


	/* *
	*@param  object $customer, string $orderType 订单状态 为空或all为所有订单 notpaid 未付款  notshipped 待发货 notreceived 待收货 complete  完成
	*int $page当前页数，int $limit 一页显示个数*@return array()
	*
	*/
	protected function _getOrderList($customer, $orderType = '', $page = 1, $limit = 10, $showfile = 0){
		$customer_id = $customer->getId();
		$orders = array();
		/** @var $orderCollection Mage_Sales_Model_Mysql4_Order_Collection */
		try {
		  $orderCollection = Mage::getModel("sales/order")->getCollection()->addFieldToFilter('customer_id',$customer_id);
		  }
		catch (Mage_Core_Exception $e) {
      echo json_encode ( array (
          'status' => false,
          'code' => 1,
          'message' => $e->getMessage()
        ));
        return ;
    	}

		try {
			if($orderType == '' || $orderType == 'all'){
			  ;
			} else  { //除了全部订单，其它的都要屏蔽cancel
					$orderCollection->addFieldToFilter('status',array('neq'=>'canceled'))
		        ->addFieldToFilter('status',array('neq'=>'closed'))
		        ->addFieldToFilter('status',array('neq'=>'holded'));
			}
			$invoices =  Mage::getResourceModel('sales/order_invoice_collection');
			$invoices->getSelect()->joinLeft(array('order' => Mage::getModel('core/resource')->getTableName('sales/order')), 'order.entity_id=main_table.order_id', array('customer_id' => 'customer_id'));
			$invoices->addFieldToFilter('customer_id',$customer_id);
			if(count($invoices)>0){
				foreach($invoices as $i){
					$orderid = $i->getOrderId();
					$invoicesorderincrementid[] = Mage::getModel('sales/order')->load($orderid)->getIncrementId();
				}
			}
			$shipment =  Mage::getResourceModel('sales/order_shipment_collection')->addFieldToFilter('customer_id',$customer_id);

			if(count($shipment)>0){
				$shipmentorderincrementid = array();
				foreach($shipment as $s){
					$orderid = $s->getOrderId();
					$shipmentorderincrementid[] = Mage::getModel('sales/order')->load($orderid)->getIncrementId();
				}
			}

			if($orderType == 'notpaid'){
				$orderCollection->addFieldToFilter('increment_id',array('nin'=>$invoicesorderincrementid));
			}
			if($orderType == 'notshipped'){
				$orderCollection->addFieldToFilter('increment_id',array('nin'=>$shipmentorderincrementid));
			}
			if($orderType == "notreceived"){
				if(count($shipmentorderincrementid)>0){
					$orderCollection->addFieldToFilter('increment_id',array('in'=>$shipmentorderincrementid));
				}
			}
			if($orderType == "complete"){
				$orderCollection->addFieldToFilter('status','complete');
				/*应该不需要
				if((count($invoicesorderincrementid)>0) && (count($shipmentorderincrementid)>0)){
					$intersection = array_intersect($invoicesorderincrementid,$shipmentorderincrementid);
					$orderCollection->addFieldToFilter('increment_id',array('in'=>$intersection));
				}*/
			}
			$orderCollection->setPageSize($limit)->setCurPage($page);

			$orderCollection->setOrder('increment_id','desc');
			$orderGrandTotal = 0;
			$orderSubTotal = 0;
			$orderQtyTotal = 0;
			foreach ($orderCollection as $order) {
				$orderGrandTotal = $orderSubTotal + $order->getGrandTotal();
				$orderSubTotal = $orderSubTotal + $order->getSubtotal();
				$orderQtyTotal = $orderQtyTotal + $order->getTotalQtyOrdered();
			}
			$orders['orderCount'] = $orderCollection->getSize();
			$orders['orderGrandTotal'] = $orderGrandTotal;
			$orders['orderSubTotal'] = $orderSubTotal;
			$orders['orderQtyTotal'] = $orderQtyTotal;

		} catch (Mage_Core_Exception $e) {
			echo json_encode ( array (
					'status' => false,
					'code' => 2,
					'message' => $e->getMessage()
				));
				return ;
		}
		foreach ($orderCollection as $order) {
			$data = array();
			$data = $this->_baseInfo($order);
			$shipment = $order->getShipmentsCollection()->getFirstItem();
			$invoicees = $order->getInvoiceCollection()->getFirstItem();
			$data['isPaid'] = false;
			if(($invoicees->getIncrementId())){
				$data['isPaid'] = true;
			}
			$data['isShipped'] = false;
			if(($shipment->getIncrementId())){
				$data['isShipped'] = true;
			}
			$data['shipment_increment_id'] = $shipment->getIncrementId();
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
				$productname[$i]['id']= $_product->getId();
				$productname[$i]['sku']= $_product->getSku();
				$productname[$i]['name']= $item->getName();
				$productname[$i]['price']= $item->getPrice();
				$productname[$i]['qty']= $item->getQtyOrdered();

				if(($_product->getImage() == 'no_selection') || (!$_product->getImage())){
					$productname[$i]['image_url']= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/images/catalog/product/placeholder/image.jpg';
				    $productname[$i]['image_thumbnail_url']=  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/images/catalog/product/placeholder/image.jpg';
				}else{
					$productname[$i]['image_url']= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $_product->getImage();
					$productname[$i]['image_thumbnail_url'] = Mage::getModel ( 'catalog/product_media_config' )->getMediaUrl( $_product->getThumbnail() );
				}
				$options = $item->getProductOptions();

        $rOptions = array ();
        if ($options) {
          if (isset ( $options ['options'] )) {
            $rOptions = array_merge ( $rOptions, $options ['options'] );
          }
          if (isset ( $options ['additional_options'] )) {
            $rOptions = array_merge ( $rOptions, $options ['additional_options'] );
          }
          if (! empty ( $options ['attributes_info'] )) {
            $rOptions = array_merge ( $rOptions, $options ['attributes_info'] );
          }
          foreach($rOptions as $j => $option){
              //对文件类型的处理，清除字段减少流量
              if ($rOptions[$j]['option_type']='file')
                $rOptions[$j]['option_value']='';
          }
        }
				$productname[$i]['options'] = $rOptions;
			}
			$data['products'] = $productname;
			$orders['list'][] = $data;
		}
		return $orders;
	}

	public function countAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();

		if (!$customer->getId()) {
		   echo json_encode ( array (
		      'status' => false,
					'code' => 2,
					'message' => 'customer_not_login'
			));
			return ;
		}
		$orderlist = array();

		//未付款
		$orderlist['notpaid'] = $this->_getOrderCount($customer,'notpaid');
		//待发货
		$orderlist['notshipped'] = $this->_getOrderCount($customer,'notshipped');
		//待收货
		$orderlist['notreceived'] = $this->_getOrderCount($customer,'notreceived');
		//已完成
		$orderlist['complete'] = $this->_getOrderCount($customer,'complete');
		//所有订单
		$orderlist['all'] = $this->_getOrderCount($customer,'');
		$result = Mage::helper('core')->jsonEncode($orderlist);
		echo $result;
	}

	/* *
	*@param  object $customer, string $orderType 订单状态 为空所有订单 notpaid 未付款  notshipped 待发货 notreceived 待收货 complete  完成
	*@return array()
	*
	*/
	protected function _getOrderCount($customer, $orderType = ''){
		$customer_id = $customer->getId();
		$orders = array();
		$orderCount = 0;
		try {
		  $orderCollection = Mage::getModel("sales/order")->getCollection()->addFieldToFilter('customer_id',$customer_id);
		  }
		catch (Mage_Core_Exception $e) {
      echo json_encode ( array (
          'status' => false,
          'code' => 2,
          'message' => $e->getMessage()
        ));
        return ;
    	}
		try {
			if($orderType == '' || $orderType == 'all'){
			  ;
			} else  {
					$orderCollection->addFieldToFilter('status',array('neq'=>'canceled'))
		        ->addFieldToFilter('status',array('neq'=>'closed'))
		        ->addFieldToFilter('status',array('neq'=>'holded'));
			}
			$invoices =  Mage::getResourceModel('sales/order_invoice_collection');
			$invoices->getSelect()->joinLeft(array('order' => Mage::getModel('core/resource')->getTableName('sales/order')), 'order.entity_id=main_table.order_id', array('customer_id' => 'customer_id'));
			$invoices->addFieldToFilter('customer_id',$customer_id);

			if(count($invoices)>0){
				$invoicesorderincrementid = array();
				foreach($invoices as $i){
					$orderid = $i->getOrderId();
					$invoicesorderincrementid[] = Mage::getModel('sales/order')->load($orderid)->getIncrementId();
				}
			}
			$shipment =  Mage::getResourceModel('sales/order_shipment_collection')->addFieldToFilter('customer_id',$customer_id);

			if(count($shipment)>0){
				$shipmentorderincrementid = array();
				foreach($shipment as $s){
					$orderid = $s->getOrderId();
					$shipmentorderincrementid[] = Mage::getModel('sales/order')->load($orderid)->getIncrementId();
				}
			}

			if($orderType == 'notpaid'){
				$orderCollection->addFieldToFilter('increment_id',array('nin'=>$invoicesorderincrementid));
			}
			if($orderType == 'notshipped'){
				$orderCollection->addFieldToFilter('increment_id',array('nin'=>$shipmentorderincrementid));
			}
			if($orderType == "notreceived"){
				if(count($shipmentorderincrementid)>0){
					$orderCollection->addFieldToFilter('increment_id',array('in'=>$shipmentorderincrementid));
				}
			}
			if($orderType == "complete"){
				$orderCollection->addFieldToFilter('status','complete');
			}
			$orderCount = $orderCollection->getSize();
		} catch (Mage_Core_Exception $e) {
			echo json_encode ( array (
					'status' => false,
					'code' => 2,
					'message' => $e->getMessage()
				));
				return ;
		}
		return $orderCount;
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
					'status' => false,
					'code' => 1,
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
					'status' => false,
					'code' => 2,
					'message' => 'Order increment_id Error'
			));
			return ;
		}

		$shipment = $order->getShipmentsCollection()->getFirstItem();
		$invoicees = $order->getInvoiceCollection()->getFirstItem();

		if ($order->getGiftMessageId() > 0) {
            $order->setGiftMessage(
                Mage::getSingleton('giftmessage/message')->load($order->getGiftMessageId())->getMessage()
            );
        }
		$data = $this->_baseInfo($order);
    $data['shipment_increment_id'] = $shipment->getIncrementId();
    $data['invoice_increment_id'] = null;
    if ($order->hasInvoices()) {
      $invIncrementIDs = array();
      foreach ($order->getInvoiceCollection() as $inv) {
        $data['invoice_increment_id'] = $inv->getIncrementId();
      }
    }

    $data['shipping_address'] = $this->_getAttributes($order->getShippingAddress(), 'order_address');
    $data['billing_address']  = $this->_getAttributes($order->getBillingAddress(), 'order_address');

		$data = Mage::helper('core')->jsonEncode($data);
		$this->getResponse()->setBody(urldecode($data));
	}

	protected function _initOrder($orderIncrementId)
    {
        $order = Mage::getModel('sales/order');

        /* @var $order Mage_Sales_Model_Order */

        $order->loadByIncrementId($orderIncrementId);

        if (!$order->getId()) {
			echo json_encode ( array (
					'status' => false,
					'code' => 2,
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
					'status' => true,
					'code' => 0,
					'message' => "comment add sucessfully"
			));
			return ;
        } catch (Mage_Core_Exception $e) {
			echo json_encode ( array (
					'status' => false,
					'code' => 2,
					'message' => $e->getMessage()
			));
			return ;
        }
	}

	public function shipmentListAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
		   echo json_encode ( array (
					'status' => false,
					'code' => 1,
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
					'status' => false,
					'code' => 2,
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
					'status' => false,
					'code' => 1,
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
					'status' => false,
					'code' => 2,
					'message' => 'shipment increment_id Error'
			));
			return ;
		}
        /* @var $shipment Mage_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
			echo json_encode ( array (
					'status' => false,
					'code' => 3,
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
					'status' => false,
					'code' => 1,
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
					'status' => false,
					'code' => 2,
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

    //订单重新支付
  public function repayAction()  {
    $orderId = $this->getRequest ()->getParam ( "orderid" );
    $repay = $this->getRequest ()->getParam ( "repay" );

    try {
      if (isset($repay) && $repay == 1) {
        //重新支付操作
        $repay = $repay;
        } else  {
        return;
        }
      if (isset($orderId) && $orderId > '') {
          $orderId = $orderId;
        } else {
          $session = Mage::getSingleton('checkout/session');;
          $orderId = $session->getLastRealOrderId();
        }

      $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

      if (!$order->getId()) {
          Mage::throwException(Mage::helper('weixinapp')->__('No order for processing'));
        }
      if ($order->getPayment()->getMethodInstance()->getCode() == 'weixinapp')  {
        $payment = Mage::getModel('weixinapp/payment');
        $config = $payment->prepareConfig();
        $total_fee = $order->getGrandTotal()*100;
        $config['body'] = '订单#'.$orderId.'-执业印章之家';
        $config['order_id'] = $orderId;
        $config['total_fee'] = $total_fee;
        $app = Mage::getModel('weixinapp/app');
        echo json_encode( $app->payment($config));
        } else  {
       echo json_encode ( array (
                     'status' => false,
                     'code' => 1,
                     'message' => '重新支付功能暂时只支持微信App支付。'
                 ));
        }
    } catch (Mage_Core_Exception $e) {
        $this->_getCheckout()->addError($e->getMessage());
    } catch(Exception $e) {
        Mage::logException($e);
    }
  }
	/*
	* @param int invoice_increment_id
	*/
	public function invoiceInfoAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
		   echo json_encode ( array (
					'status' => false,
					'code' => 1,
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
					'status' => false,
					'code' => 2,
					'message' => 'invoice increment_id Error'
			));
			return ;
		}


        /* @var Mage_Sales_Model_Order_Invoice $invoice */

        if (!$invoice->getId()) {
			echo json_encode ( array (
					'status' => false,
					'code' => 3,
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

	protected  function _baseInfo($order) {
    $data = array();
    /*取全部属性，暂时不用
    $data = $this->_getAttributes($order, 'order');
    */
    $data['increment_id'] = $order->getIncrementId();
    $data['status'] = $order->getStatus();
    $data['status_label'] = $order->getStatusLabel();
    $data['state'] = $order->getState();
    $data['customer_id'] = $order->getCustomerId();
    $data['grand_total'] = $order->getGrandTotal();
    $data['subtotal'] = $order->getSubtotal();
    $data['shipping_amount'] = $order->getShippingAmount();
    $data['total_qty_ordered'] = $order->getTotalQtyOrdered();
    $data['created_at'] = $order->getCreatedAt();
    $data['updated_at'] = $order->getUpdatedAt();
    $data['payment_code'] = $order->getPayment()->getMethodInstance()->getCode();
    $data['payment_title'] = $order->getPayment()->getMethodInstance()->getTitle();
    $productname = array();
    foreach ($order->getAllItems() as $i=>$item){
    /*取全部属性，暂时不用
			$productinfos = $this->_getAttributes($item, 'order_item');
			$product_options = $productinfos['product_options'];
			$product_optionsarray = unserialize($product_options);
			$options = $product_optionsarray['options'];
    */
      $productid = $item->getProduct()->getId();
      $_product = Mage::getModel('catalog/product')->load($productid);
      $productname[$i]['id']= $_product->getId();
      $productname[$i]['sku']= $_product->getSku();
      $productname[$i]['item_id']= $item->getItemId();
      $productname[$i]['quote_item_id']= $item->getQuoteItemId();
      $productname[$i]['product_id']= $item->getProductId();
      $productname[$i]['name']= $item->getName();
      $productname[$i]['price']= $item->getPrice();
      $productname[$i]['price_incl_tax']= $item->getPriceInclTax();
      $productname[$i]['qty']= $item->getQtyOrdered();
      $productname[$i]['qty_ordered']= $item->getQtyOrdered();

      if(($_product->getImage() == 'no_selection') || (!$_product->getImage())){
        $productname[$i]['image_url']= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/images/catalog/product/placeholder/image.jpg';
          $productname[$i]['image_thumbnail_url']=  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/images/catalog/product/placeholder/image.jpg';
      }else{
        $productname[$i]['image_url']= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $_product->getImage();
        $productname[$i]['image_thumbnail_url'] = Mage::getModel ( 'catalog/product_media_config' )->getMediaUrl( $_product->getThumbnail() );
      }
      $options = $item->getProductOptions();

      $rOptions = array ();
      if ($options) {
        if (isset ( $options ['options'] )) {
          $rOptions = array_merge ( $rOptions, $options ['options'] );
        }
        if (isset ( $options ['additional_options'] )) {
          $rOptions = array_merge ( $rOptions, $options ['additional_options'] );
        }
        if (! empty ( $options ['attributes_info'] )) {
          $rOptions = array_merge ( $rOptions, $options ['attributes_info'] );
        }
        foreach($rOptions as $j => $option){
            //对文件类型的处理，清除字段减少流量
            if ($rOptions[$j]['option_type']='file')
              $rOptions[$j]['option_value']='';
        }
      }
      $productname[$i]['options'] = $rOptions;
    }
    $data['products'] = $productname;
    return $data;
	}

}
