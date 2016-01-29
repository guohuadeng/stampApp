<?php

class FME_Productattachments_Block_ListAttachments extends Mage_Core_Block_Template {
     
    public function _prepareLayout() {
		
		parent::_prepareLayout();
		
		$_helper = Mage::helper('productattachments');
		$currentCat = array();
		$currentAttach = array();
		
		if (Mage::registry('fme_pa_cat')) {
			
			$currentCat = Mage::registry('fme_pa_cat');//Mage::getSingleton('core/session')->getCurrentFmePaCat();
		}
		
		if (Mage::registry('fme_pa_att')) {
			
			$currentAttach = Mage::registry('fme_pa_att');//Mage::getSingleton('core/session')->getCurrentFmePaAtt();
		}
		
		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');

		$breadcrumbs->addCrumb('home', array(
			'label' => Mage::helper('cms')->__('Home'),
			'title' => Mage::helper('cms')->__('Go to Home Page'),
			'link' => Mage::getBaseUrl()
		));

		$breadcrumbs->addCrumb('productattachments', array(
			'label' => Mage::helper('productattachments')->__('Product Attachments'),
			'title' => Mage::helper('productattachments')->__('Product Attachments'),
			'link' => (!empty($currentCat)? Mage::getUrl('*/*/list'): false),
			'last' => (!empty($currentCat)? false: true),
		));
		
		if (!empty($currentCat)) {
			
			
			$parentCat = $_helper->checkParent($currentCat);
			if (is_object($parentCat) && $parentCat->getData()) {
				
				$breadcrumbs->addCrumb('productattachments_par_cat', array(
					'label' => $parentCat->getCategoryName(),
					'title' => $parentCat->getCategoryName(),
					'link' => (!empty($currentCat)? Mage::getUrl('*/*/view', array('u' => $parentCat->getCategoryUrlKey())): false),
					'last' => (!empty($currentCat)? false: true),
				));
			}
			
			$breadcrumbs->addCrumb('productattachments_cat', array(
				'label' => $currentCat->getCategoryName(),
				'title' => $currentCat->getCategoryName(),
				'link' => (!empty($currentAttach)? true: false),
				'last' => (!empty($currentAttach)? false: true),
			));
		}
		
		if (!empty($currentAttach)) {
			
			$currentCat = $_helper->getAllLevels($currentAttach);
			
			$breadcrumbs->addCrumb('productattachments', array(
				'label' => Mage::helper('productattachments')->__('Product Attachments'),
				'title' => Mage::helper('productattachments')->__('Product Attachments'),
				'link' => (!empty($currentCat)? Mage::getUrl('*/*/list'): false),
				'last' => (!empty($currentCat)? false: true),
			));
		
			
			if (!empty($currentCat)) {
				
				if (isset($currentCat['curr_par_cat'])) {
					$parentCat = $currentCat['curr_par_cat'];
					$breadcrumbs->addCrumb('productattachments_par_cat', array(
						'label' => $parentCat->getCategoryName(),
						'title' => $parentCat->getCategoryName(),
						'link' => (!empty($currentCat)? Mage::getUrl('*/*/view', array('u' => $parentCat->getCategoryUrlKey())): false),
						'last' => (!empty($currentCat)? false: true),
					));
				}
				
				$breadcrumbs->addCrumb('productattachments_cat', array(
					'label' => $currentCat['curr_cat']->getCategoryName(),
					'title' => $currentCat['curr_cat']->getCategoryName(),
					'link' => (!empty($currentAttach)? Mage::getUrl('*/*/view', array('u' => $currentCat['curr_cat']->getCategoryUrlKey())): false),
					'last' => (!empty($currentAttach)? false: true),
				));
				
			}
			
			$breadcrumbs->addCrumb('productattachments_att', array(
				'label' => $currentAttach->getTitle(),
				'title' => $currentAttach->getTitle(),
				'link' => false,
				'last' => true,
			));
		}
	}
	
    public function countSubCategories($cat) {
		
		$countCollection = Mage::getModel('productattachments/productcats')
		                     ->getCollection()
		                     ->addStoreFilter(Mage::app()->getStore()->getId())
		                     ->addFieldToFilter('main_table.parent_category_id', $cat)
		                     ->addStatusFilter(true);
		
		return $countCollection->count();
	}
	
	public function countAttachments($id) {
		
		$count = Mage::getModel('productattachments/productattachments')
			->getCollection()
			->addFieldToFilter('main_table.cat_id', $id)
			->addStoreFilter(Mage::app()->getStore()->getId())
			->addEnableFilter(true);
			
		return $count->count();
	}
	
	public function view() {
		
		$param = $this->getRequest()->getParam('u');
		$data = array();
		
		$data = Mage::getModel('productattachments/productcats')
			->load($param,'category_url_key');
		
		
		return $data;
	}
	
	public function attachments() {
		
		$param = $this->getRequest()->getParam('v');
		
		$data = Mage::getModel('productattachments/productattachments')->load($param);
		
		return $data;
	}
}
