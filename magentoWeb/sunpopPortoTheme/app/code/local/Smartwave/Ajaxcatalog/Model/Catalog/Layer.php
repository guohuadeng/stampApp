<?php

class Smartwave_Ajaxcatalog_Model_Catalog_Layer extends Mage_Catalog_Model_Layer
{
   
   
	/*
	* Add Filter in product Collection for new price
	*
	* @return object
	*/
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->getCurrentCategory()->getProductCollection();
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
		
		$this->currentRate = $collection->getCurrencyRate();
		$max=$this->getMaxPriceFilter();
		$min=$this->getMinPriceFilter();

        $where = '1=1 ';
        if(isset($min) && $min){
            $where .= ' AND final_price >= "'.$min.'"';
        }
        if(isset($max) && $max){
            $where .= ' AND final_price <= "'.$max.'"';
        }
        $where ='('.$where.') OR (final_price is NULL)';
        $collection->getSelect()->where($where);

        return $collection;
    }
	
	
	/*
	* convert Price as per currency
	*
	* @return currency
	*/
	public function getMaxPriceFilter(){
		if(isset($_GET['max']))
			return ceil($_GET['max']/$this->currentRate);
		return 0;
	}
	
	
	/*
	* Convert Min Price to current currency
	*
	* @return currency
	*/
	public function getMinPriceFilter(){
		if(isset($_GET['min']))
			return floor($_GET['min']/$this->currentRate);
		return 0;
	}
    
	
}
