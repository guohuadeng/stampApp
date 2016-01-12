<?php

class Iksanika_Ordernumber_Model_Observer
{
    public function processBlockHtmlBefore($observer) 
    {
//        $block = $observer->getBlock();
// rewrite all 4 grids to set text type for increments
// add compatibility with extenede order grid
        
//        $productGridClass = Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_grid');
//        if ($productGridClass == get_class($block)) {
//
//  ->getColumn()->setType(text);
//        }
        
        return $this;
    }
    
    
    public function processDocumentSaveBefore($observer)
    {
        $type = '';
        foreach (array('invoice', 'shipment', 'creditmemo') as $t){
            if (is_object($observer->getData($t))){
                $type = $t;
            }
        }
        
        if (!$type){
             return;   
        }
        
        $doc     = $observer->getData($type);
        $order   = $doc->getOrder();
        $storeId = $order->getStore()->getStoreId();
        
        if ( !Mage::getStoreConfig('ordernumber/' . $type . '/same', $storeId)){
            return;
        }
        
        $number  = 0;
        $counter = 0;
        while (!$number) {
            $number = Mage::getStoreConfig('ordernumber/' . $type . '/prefix', $storeId) . $order->getIncrementId();
            if ($counter) {
                $number .= '-' . $counter;
            }
            
            $collection = Mage::getModel('sales/order_' . $type)
                ->getCollection()
                ->addFieldToFilter('increment_id', $number)
                ->setPageSize(1);
            
            if (count($collection)){
                $number = 0;
            }
            
            ++$counter; 
        }
        
        $doc->setIncrementId($number);
    }
} 