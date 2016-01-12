<?php

class Iksanika_Ordernumber_Model_Resource_Eav_Entity_Type 
    extends Mage_Eav_Model_Entity_Type
{
    
    // Gets not cached config row as object
    public function getConfigNotStored($path, $storeId)
    {
        $typeCode   =   $this->getEntityTypeCode();
        $config     =   Mage::getStoreConfig('ordernumber/'.$typeCode, $storeId);
        
        $scope      =   'default';
        $scopeId    =   0;
        if ($config['store_based'])
        {
            $scope      =   'stores';
            $scopeId    =   $storeId;
        }elseif ($config['website_based'])
        {
            $scope      =   'websites';
            $scopeId    =   Mage::app()->getStore($storeId)->getWebsite()->getId();
        }
        
        $collection = Mage::getResourceModel('core/config_data_collection');
        $collection->addFieldToFilter('scope', $scope);
        $collection->addFieldToFilter('scope_id', $scopeId);
        $collection->addFieldToFilter('path', 'ordernumber/'.$typeCode.'/'.$path);
        $collection->setPageSize(1);
        
        $result = Mage::getModel('core/config_data');
        if (count($collection))
        {
            $result = $collection->getFirstItem();
        }else
        {
            $result->setScope($scope);
            $result->setScopeId($scopeId);
            $result->setPath('ordernumber/'.$typeCode.'/'.$path);
        }
        
        return $result;
    }

    // generate increment id
    public function fetchNewIncrementId($storeId = null)
    {
        $incrementId    =   parent::fetchNewIncrementId($storeId);
        $typeCode       =   $this->getEntityTypeCode();
        $allowedTypes   =   array('order', 'shipment', 'invoice', 'creditmemo');
        
        if (!$incrementId) 
        {
            return false;
        }
        
        if (!Mage::getStoreConfig('ordernumber/general/enabled', $storeId) || !in_array($typeCode, $allowedTypes))
        {
            return $incrementId;
        }
        
        if (Mage::getStoreConfig('ordernumber/'.$typeCode.'/same', $storeId))
        {
            return $incrementId;
        }
        
        $config         =   Mage::getStoreConfig('ordernumber/'.$typeCode, $storeId);
        $startValue     =   max(intVal($config['start']), 0);
        $currentValue   =   $this->getConfigNotStored('counter', $storeId);
        
        if($currentValue->getValue() > 0)
        {
            if($config['counter'])
            {
                $currentValue->setValue($startValue);
                
                $configStack = Mage::getModel('core/config');
                $configStack->saveConfig('ordernumber/order/counter', 0);
                $configStack->cleanCache();
            }else
            if ($config['reset'])
            {
                $oldDate = $this->getConfigNotStored('date', $storeId);
                
                if (!$oldDate->getValue() || date($config['reset']) != date($config['reset'], strtotime($oldDate->getValue())))
                {
                    $currentValue->setValue($startValue);
                }
                $oldDate->setValue(date('Y-m-d'));
                $oldDate->save();
            }    
        }else
        {
            $currentValue->setValue($startValue);
        }
        
        $counter = max(intVal($currentValue->getValue()), 0) + max(intVal($config['increment']), 1);
        $currentValue->setValue($counter);
        $currentValue->save();
        
        if (intVal($config['padding']))
        {
            $counter = str_pad($counter, intVal($config['padding']), '0', STR_PAD_LEFT);
        }
        
        
        
        
        $patterns = array(
            'store_id'  =>  $storeId,
            'yy'        =>  date('y'),
            'yyyy'      =>  date('Y'),
            'mm'        =>  date('m'),
            'm'         =>  date('n'),
            'dd'        =>  date('d'),
            'd'         =>  date('j'),
            'counter'   =>  $counter,
        );
        
        $incrementId = $config['format'];
        foreach ($patterns as $pattern => $val)
        {
            $incrementId = str_replace('{'.$pattern.'}', $val, $incrementId);
        }
        
        return $incrementId;
    }
}
