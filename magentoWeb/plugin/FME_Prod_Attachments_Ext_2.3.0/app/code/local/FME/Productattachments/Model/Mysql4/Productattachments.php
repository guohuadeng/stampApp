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

class FME_Productattachments_Model_Mysql4_Productattachments extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the productattachments_id refers to the key field in your database table.
        $this->_init('productattachments/productattachments', 'productattachments_id');
    }
	
	public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (strcmp($value, (int)$value) !== 0) {
            $field = 'productattachments_id';
        }
        return parent::load($object, $value, $field);
    }
	
	 protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('productattachments_store'))
            ->where('productattachments_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            
            $object->setData('store_id', $storesArray);
        }
		
		$products = $this->__listProducts($object);
        if ($products) {
			
            $object->setData('product_id', $products);
        }
        
        return parent::_afterLoad($object);
        
    }
	
	private function __listProducts(Mage_Core_Model_Abstract $object) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('productattachments/productattachments_products'))
                ->where('productattachments_id = ?', $object->getId());
        $data = $this->_getReadAdapter()->fetchAll($select);
        if ($data) {
            $productsArr = array();
            foreach ($data as $_i) {
                $productsArr[] = $_i['product_id'];
            }

            return $productsArr;
        }
    }
	/**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
		
        $condition = $this->_getWriteAdapter()->quoteInto('productattachments_id = ?', $object->getId());
    	
    	$r = $this->_getReadAdapter()
				->select('p.block_name_product')
				->from(array('p' => $this->getTable('productattachments_store')))
				->where('productattachments_id = (?)', $object->getId())
				->limit(1);
		$row = $this->_getReadAdapter()->fetchRow($r);
		$block_name = '';
		if (isset($row['block_name_product'])) {
			$block_name = $row['block_name_product'];
		}	
    	$this->_getWriteAdapter()->delete($this->getTable('productattachments_store'), $condition);
		 
		foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['productattachments_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('productattachments_store'), $storeArray);
        }
		
		
			
		//Get Related Products		
		$links = $object['links'];
		if (isset($links['related'])) {
			$productIds = Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']);
			
			$this->_getWriteAdapter()->delete($this->getTable('productattachments_products'), $condition);
			
			//Save Related Products
			foreach ($productIds as $_product) {
				$newsArray = array();
				$newsArray['productattachments_id'] = $object->getId();
				$newsArray['product_id'] = $_product;
				$newsArray['block_name_product'] = $block_name;
				$this->_getWriteAdapter()->insert($this->getTable('productattachments_products'), $newsArray);
			}
		} 

        return parent::_afterSave($object);
        
    }
	
	public function updateDownloadsCounter($id){
		$attachmentsTable = Mage::getSingleton('core/resource')->getTableName('productattachments');		
		$db = $this->_getWriteAdapter();
		try {
				$db->beginTransaction();
				$db->exec("UPDATE ".$attachmentsTable." SET downloads = (downloads+1) WHERE productattachments_id = $id");
				$db->commit();
				
			} catch(Exception $e) {
				$db->rollBack();
				throw new Exception($e);
			}
	}
	
	
}
