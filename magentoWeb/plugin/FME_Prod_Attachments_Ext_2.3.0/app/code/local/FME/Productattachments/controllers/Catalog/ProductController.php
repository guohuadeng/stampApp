<?php

require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

class FME_Productattachments_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController {
  
	public function preDispatch() {
		
		parent::preDispatch();
		$this->getRequest()->setRouteName('productattachments');
		
	} 
  
  	/**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_grid')->toHtml()
        );
    }

    /**
     * Get specified tab grid
     */
    public function gridOnlyAction()
    {
        $this->_initProduct();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_' . $this->getRequest()->getParam('gridOnlyBlock'))
                ->toHtml()
        );
    }

  
	public function attachmentsGridAction() {

		$this->_initProduct();
		$this->_initProductAttachments();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.attachments')
            ->setProductRelatedAttachments($this->getRequest()->getPost('products_related_attachments', null));
        $this->renderLayout();
	}
	
	public function attachmentsAction () {
		
		$this->_initProduct();
		$this->_initProductAttachments();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.attachments')
            ->setProductRelatedAttachments($this->getRequest()->getPost('products_related_attachments', null));
        $this->renderLayout();


	}
  
	protected function _initProduct($block=null){
		static $product = null;
		if($block===false){
			$product = null;
			return;
		}
		if(!$product){
			$r = parent::_initProduct();
			if($block===true)
				$product = $r;
			return($r);
		} else {
			return($product);
		}
	}
  
    protected function _initProductAttachments() {
		
		$productattachments = Mage::getModel('productattachments/productattachments');
		Mage::register('current_product_attachments', $productattachments);
		return $productattachments;
	}
  
}

?>
