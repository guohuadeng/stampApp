<?php
/**
 * Productattachments extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Productattachments
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
 
class FME_Productattachments_Block_Cmspagesattachments extends Mage_Catalog_Block_Product_Abstract
{
	
	const DISPLAY_CONTROLS = 'productattachments/cmspagesattachments/enabled';
	protected function _tohtml()
    {
		if (!Mage::getStoreConfig(self::DISPLAY_CONTROLS)) {
			return parent::_toHtml();
		} 
		$this->setLinksforProduct();
		$this->setTemplate("productattachments/cms_attachments.phtml");
		return parent::_toHtml();
    }
	
	public function getCmsPageRelatedAttachments($categoryId = null) {
		
		$dataCurrentPage = $this->getHelper('cms/page')->getPage()->getData();
		$pageid = $dataCurrentPage['page_id'];
	
		$storeId = Mage::app()->getStore()->getId();
		
		$productattachmentsTable = Mage::getSingleton('core/resource')->getTableName('productattachments');
		//$productattachmentsStoreTable = Mage::getSingleton('core/resource')->getTableName('productattachments_store');
		$productattachmentsCategoryTable = Mage::getSingleton('core/resource')->getTableName('productattachments_cats');
		
		$collection = Mage::getModel('productattachments/productattachments')
						->getCollection()
						->addStoreFilter($storeId);	
		
		$collection->getSelect()
			->join(array('pacat' => $productattachmentsCategoryTable), 'main_table.cat_id = pacat.category_id');
		
		if ($categoryId != null) {
			
			$collection->getSelect()
				->where('main_table.cat_id = (?)', $categoryId)
				->where('pacat.category_status = (?)', 1);
		}
		
		$collection->getSelect()
			->where('main_table.cmspage_id LIKE (?)', "%{$pageid}%")
			->where('main_table.status = (?)', 1);
		//echo (string)$collection->getSelect();exit;
		return $collection;
		
	}
}
