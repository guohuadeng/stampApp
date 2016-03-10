<?php

class Smartwave_Porto_Model_Import_Cms extends Mage_Core_Model_Abstract
{
	private $_importPath;
	
	public function __construct()
    {
        parent::__construct();
		$this->_importPath = Mage::getBaseDir() . '/app/code/local/Smartwave/Porto/etc/import/';
    }
	
	public function importCms($modelString, $itemContainerNodeString, $overwrite = false)
    {
		try
		{
			$xmlPath = $this->_importPath . $itemContainerNodeString . '.xml';
			if (!is_readable($xmlPath))
			{
				throw new Exception(
					Mage::helper('porto')->__("Can't get the data file for import cms blocks/pages: %s", $xmlPath)
                );
			}
			$xmlObj = new Varien_Simplexml_Config($xmlPath);
			
			$conflictingOldItems = array();
			$i = 0;
			foreach ($xmlObj->getNode($itemContainerNodeString)->children() as $b)
			{

				$oldBlocks = Mage::getModel($modelString)->getCollection()
					->addFieldToFilter('identifier', $b->identifier)
					->load();
				
				if ($overwrite)
				{
					if (count($oldBlocks) > 0)
					{
						$conflictingOldItems[] = $b->identifier;
						foreach ($oldBlocks as $old)
							$old->delete();
					}
				}
				else
				{
					if (count($oldBlocks) > 0)
					{
						$conflictingOldItems[] = $b->identifier;
						continue;
					}
				}
				if($modelString == 'cms/page'){
                    Mage::getModel($modelString)
                        ->setTitle($b->title)
                        ->setContent($b->content)
                        ->setIdentifier($b->identifier)
                        ->setIsActive($b->is_active)
                        ->setStores(array(0))
                        ->setRootTemplate($b->root_template)
                        ->setLayoutUpdateXml($b->layout_update_xml)
                        ->save();
                    
                }else {
				    Mage::getModel($modelString)
					    ->setTitle($b->title)
					    ->setContent($b->content)
					    ->setIdentifier($b->identifier)
					    ->setIsActive($b->is_active)
					    ->setStores(array(0))
					    ->save();
                }
				$i++;
			}
			
			if ($i)
			{
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('porto')->__('%s item(s) was(were) imported.', $i)
				);
			}
			else
			{
				Mage::getSingleton('adminhtml/session')->addNotice(
					Mage::helper('porto')->__('No items were imported')
				);
			}
			
			if ($overwrite)
			{
				if ($conflictingOldItems)
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('porto')
						->__('Items (%s) with the following identifiers were overwritten:<br />%s', count($conflictingOldItems), implode(', ', $conflictingOldItems))
					);
			}
			else
			{
				if ($conflictingOldItems)
					Mage::getSingleton('adminhtml/session')->addNotice(
						Mage::helper('porto')
						->__('Unable to import items (%s) with the following identifiers (they already exist in the database):<br />%s', count($conflictingOldItems), implode(', ', $conflictingOldItems))
					);
			}
		}
		catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			Mage::logException($e);
		}
    }
	
}
