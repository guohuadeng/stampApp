<?php

class Smartwave_Filterproducts_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/catalogextensions?id=15 
    	 *  or
    	 * http://site.com/catalogextensions/id/15 	
    	 */
    	/* 
		$catalogextensions_id = $this->getRequest()->getParam('id');

  		if($catalogextensions_id != null && $catalogextensions_id != '')	{
			$catalogextensions = Mage::getModel('catalogextensions/catalogextensions')->load($catalogextensions_id)->getData();
		} else {
			$catalogextensions = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($catalogextensions == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$catalogextensionsTable = $resource->getTableName('catalogextensions');
			
			$select = $read->select()
			   ->from($catalogextensionsTable,array('catalogextensions_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$catalogextensions = $read->fetchRow($select);
		}
		Mage::register('catalogextensions', $catalogextensions);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
	public function bestsellersAction()
    {

		$url = Mage::getUrl('no-route'); 

		$enable = Mage::getStoreConfig('filterproducts/config1/active');
		if($enable != 1) 
		{
			Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}	
		else
		{
		$this->loadLayout(); 
		$this->getLayout()->getBlock('head')->setTitle('Besesellers');    
		$this->renderLayout();
		}
    }
	public function featuredAction()
    {

		$url = Mage::getUrl('no-route'); 

		$enable = Mage::getStoreConfig('filterproducts/config2/active');
		if($enable != 1) 
		{
			Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}

		$this->loadLayout(); 
		$this->getLayout()->getBlock('head')->setTitle('Featured Products');    
		$this->renderLayout();
    }
	public function mostviewedAction()
    {

		$url = Mage::getUrl('no-route'); 

		$enable = Mage::getStoreConfig('filterproducts/config3/active');
		if($enable != 1) 
		{
			Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}

		$this->loadLayout(); 
		$this->getLayout()->getBlock('head')->setTitle('Mostviewed products');    
		$this->renderLayout();
    }
	public function newproductAction()
    {

		$url = Mage::getUrl('no-route'); 

		$enable = Mage::getStoreConfig('filterproducts/config4/active');
		if($enable != 1) 
		{
			Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}

		$this->loadLayout(); 
		$this->getLayout()->getBlock('head')->setTitle('New products');    
		$this->renderLayout();
    }
	public function latestAction()
    {

		$url = Mage::getUrl('no-route'); 

		$enable = Mage::getStoreConfig('filterproducts/config6/active');
		if($enable != 1) 
		{
			Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}

		$this->loadLayout(); 
		$this->getLayout()->getBlock('head')->setTitle('Latest Products');    
		$this->renderLayout();
    }
	public function saleAction()
    {

		$url = Mage::getUrl('no-route'); 

		$enable = Mage::getStoreConfig('filterproducts/config7/active');

		if($enable != 1) 
		{
			Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}

		$this->loadLayout(); 
		$this->getLayout()->getBlock('head')->setTitle('Sale Products');    
		$this->renderLayout();
    }
	
	public function allproductAction()
    {

		$url = Mage::getUrl('no-route'); 

		$enable = Mage::getStoreConfig('filterproducts/config5/active');
		if($enable != 1) 
		{
			Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}

		$this->loadLayout(); 
		$this->getLayout()->getBlock('head')->setTitle('All products');    
		$this->renderLayout();
    }
	
	public function promotionalAction()
    {

		$url = Mage::getUrl('no-route'); 

		$enable = Mage::getStoreConfig('filterproducts/config6/active');
		if($enable != 1) 
		{
			Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}

		$this->loadLayout(); 
		$this->getLayout()->getBlock('head')->setTitle('Promotional Products');    
		$this->renderLayout();
    }

	public function recentlyorderedAction()
    {

		$url = Mage::getUrl('no-route'); 

		$enable = Mage::getStoreConfig('filterproducts/config7/active');
		if($enable != 1) 
		{
			Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}

		$this->loadLayout(); 
		$this->getLayout()->getBlock('head')->setTitle('Recently Ordered Products');    
		$this->renderLayout();
    }
	
}