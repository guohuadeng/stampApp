<?php

class Smartwave_Ajaxcatalog_Block_Catalog_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price 
{
    	
	public $_currentCategory;
	public $_searchSession;
	public $_productCollection;
	public $_maxPrice;
	public $_minPrice;
	public $_currMinPrice;
	public $_currMaxPrice;
	public $_imagePath;
	
	
	/*
	* 
	* Set all the required data that our slider will require
	* Set current _currentCategory, _searchSession, setProductCollection, setMinPrice, setMaxPrice, setCurrentPrices, _imagePath
	* 
	* @set all required data
	* 
	*/
	public function __construct(){
	    $this->_currentCategory = Mage::registry('current_category');
        $this->_searchSession = Mage::getSingleton('catalogsearch/session');
        $this->setProductCollection();
        $this->setMinPrice();
        $this->setMaxPrice();
        $this->setCurrentPrices();
        $this->_imagePath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'smartwave/ajaxcatalog/';            
        parent::__construct();            
	}
	
	/*
	* 
	* Check whether the slider is enabled.
	*
	* @return boolean
	* 
	*/
	public function getSliderStatus(){
		if(Mage::getStoreConfig('ajax_catalog/price_slider_settings/price_slider_en'))
			return true;
		else
			return false;			
	}
	 
	
	/*
	* Fetch Styles for price text Box
	*
	* @return styles
	*/
	public function getPriceBoxStyle(){
		$styles = trim($this->getConfig('ajax_catalog/price_slider_conf/textBoxCss'));
		if($styles == ''){
			$styles = "width:50px; padding:5px; border-radius:5px; ";
		}
		return $styles;
	}
	
	public function getGoBtnText(){
		$name = trim($this->getConfig('ajax_catalog/price_slider_conf/goBtnText'));
		if($name == ''){
			$name = "Go";
		}
		return $name;
	}

	public function getGoBtnStyle(){
		$styles = trim($this->getConfig('ajax_catalog/price_slider_conf/goBtnCss'));
		return $styles;
	}
	
	public function isTextBoxEnabled(){
		return $this->getConfig('ajax_catalog/price_slider_conf/textbox');	
	}
	
	
	public function getPriceDisplayType(){
		$textBoxStyle = $this->getPriceBoxStyle();
		$goBtnStyle = $this->getGoBtnStyle();
		if($this->isTextBoxEnabled()){
			$html = '
				<div class="text-box">
					<span>'.$this->__('from').'</span> <input type="text" name="min" id="minPrice" class="priceTextBox minPrice" value="'.$this->getCurrMinPrice().'" style="'.$textBoxStyle.'" /> 
					<span>'.$this->__('to').'</span> <input type="text" name="max" id="maxPrice" class="priceTextBox maxPrice" value="'.$this->getCurrMaxPrice().'" style="'.$textBoxStyle.'" />
					<input type="button" value="'.$this->getGoBtnText().'" name="go" class="go" style="'.$goBtnStyle.'" />
					<input type="hidden" id="amount" class="price-amount" style="background:none; border:none;" value="'.$this->getCurrencySymbol().$this->getCurrMinPrice()." - ".$this->getCurrencySymbol().$this->getCurrMaxPrice().'" />

				</div>';
		}else{
			$html = '<p>
					<input type="text" id="amount" class="price-amount" readonly="readonly" style="background:none; border:none;" value="'.$this->getCurrencySymbol().$this->getCurrMinPrice()." - ".$this->getCurrencySymbol().$this->getCurrMaxPrice().'" />
					</p>';	
		}
		return $html;
	}
	
	/**
	*
	* Prepare html for slider and add JS that incorporates the slider.
	*
	* @return html
	*
	*/
	
	public function getHtml(){		
		if($this->getSliderStatus()){
			$text='<div class="price price-filter-slider">
				<div>
					<div class="slider-range"></div>
                    '.$this->getPriceDisplayType().'
					
				</div><div class="clearer"></div></div>'.$this->getSliderJs();	
			
			return $text;
		} else {
            return parent::_toHtml();
        }	
	}
	
	/*
	* Prepare query string that was in the original url 
	*
	* @return queryString
	*/
	public function prepareParams(){
		$url="";
	
		$params=$this->getRequest()->getParams();
		foreach ($params as $key=>$val)
			{
					if($key=='id'){ continue;}
					if($key=='min'){ continue;}
					if($key=='max'){ continue;}
					$url.='&'.$key.'='.$val;		
			}		
		return $url;
	}
	
	/*
	* Fetch Current Currency symbol
	* 
	* @return currency
	*/
	public function getCurrencySymbol(){
		return Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
	}
	
	/*
	* Fetch Current Minimum Price
	* 
	* @return price
	*/
	public function getCurrMinPrice(){
		if($this->_currMinPrice > 0){
			$min = $this->_currMinPrice;
		}else{
			$min = $this->_minPrice;
		}
		return $min;
	}
	
	/*
	* Fetch Current Maximum Price
	* 
	* @return price
	*/
	public function getCurrMaxPrice(){
		if($this->_currMaxPrice > 0){
			$max = $this->_currMaxPrice;
		} else{
			$max = $this->_maxPrice;
		}
		return $max;
	}
	
	/*
	* Get Slider Configuration TimeOut
	* 
	* @return timeout
	*/
	public function getConfigTimeOut(){
		return $this->getConfig('ajax_catalog/price_slider_conf/timeout');
	}
	
	
	/*
	* Gives you the current url without parameters
	* 
	* @return url
	*/
	public function getCurrentUrlWithoutParams(){
		//$baseUrl = explode('?',Mage::getUrl('',array('_current'=>true,'_use_rewrite'=>true)));
        $baseUrl = explode('?',Mage::helper('core/url')->getCurrentUrl());
		$baseUrl = $baseUrl[0];
		return $baseUrl;
	}
	
	/*
	* Check slider Ajax enabled
	* 
	* @return boolean
	*/
	public function isAjaxSliderEnabled(){
		return $this->getConfig('ajax_catalog/price_slider_settings/slider');
	}
	
	
	public function getOnSlideCallbacks(){
		return $this->getConfig('ajax_catalog/price_slider_conf/onSlide');	
	}
	
	/*
	* Get JS that brings the slider in Action
	* 
	* @return JavaScript
	*/
	public function getSliderJs(){
		
		$baseUrl = $this->getCurrentUrlWithoutParams();
		$timeout = $this->getConfigTimeOut();
		$styles = $this->prepareCustomStyles();
		
		if($this->isAjaxSliderEnabled()){
			$ajaxCall = 'sliderAjax(url);';
		}else{
			$ajaxCall = 'window.location=url;';
		}
		
		if($this->isTextBoxEnabled()){
			$updateTextBoxPriceJs = '
							// Update TextBox Price
							$(".minPrice").val(newMinPrice); 
							$(".maxPrice").val(newMaxPrice);';
		}
		
		
		$html = '
			<script type="text/javascript">
				jQuery(function($) {
					var newMinPrice, newMaxPrice, url, temp;
					var categoryMinPrice = '.$this->_minPrice.';
					var categoryMaxPrice = '.$this->_maxPrice.';
                    
					function isNumber(n) {
					  return !isNaN(parseFloat(n)) && isFinite(n);
					}
					
					$(".priceTextBox").focus(function(){
						temp = $(this).val();	
					});
					
					$(".priceTextBox").keyup(function(){
						var value = $(this).val();
						if(value!="" && !isNumber(value)){
							$(this).val(temp);	
						}
					});
					
					$(".priceTextBox").keypress(function(e){
						if(e.keyCode == 13){
							var value = $(this).val();
							if(value < categoryMinPrice || value > categoryMaxPrice){
								$(this).val(temp);	
							}
							url = getUrl($(".minPrice").val(), $(".maxPrice").val());
							'.$ajaxCall.'	
						}	
					});
					
					$(".priceTextBox").blur(function(){
						var value = $(this).val();
						if(value < categoryMinPrice || value > categoryMaxPrice){
							$(this).val(temp);	
						}
						
					});
					
					$(".go").click(function(){
						url = getUrl($(".minPrice").val(), $(".maxPrice").val());
						'.$ajaxCall.'	
					});					
					$( ".slider-range" ).slider({
						range: true,
						min: categoryMinPrice,
						max: categoryMaxPrice,
						values: [ '.$this->getCurrMinPrice().', '.$this->getCurrMaxPrice().' ],
						slide: function( event, ui ) {
							newMinPrice = ui.values[0];
							newMaxPrice = ui.values[1];
							
							$( ".price-amount" ).val( "'.$this->getCurrencySymbol().'" + newMinPrice + " - '.$this->getCurrencySymbol().'" + newMaxPrice );
							
							'.$updateTextBoxPriceJs.'
							
						},stop: function( event, ui ) {
							
							// Current Min and Max Price
							var newMinPrice = ui.values[0];
							var newMaxPrice = ui.values[1];
							
							// Update Text Price
							$( ".price-amount" ).val( "'.$this->getCurrencySymbol().'"+newMinPrice+" - '.$this->getCurrencySymbol().'"+newMaxPrice );
							
							'.$updateTextBoxPriceJs.'
							
							url = getUrl(newMinPrice,newMaxPrice);
							if(newMinPrice != '.$this->getCurrMinPrice().' && newMaxPrice != '.$this->getCurrMaxPrice().'){
								clearTimeout(timer);
								//window.location= url;
								
							}else{
									timer = setTimeout(function(){
										'.$ajaxCall.'
									}, '.$timeout.');     
								}
						}
					});
					
					function getUrl(newMinPrice, newMaxPrice){
						return "'.$baseUrl.'"+"?min="+newMinPrice+"&max="+newMaxPrice+"'.$this->prepareParams().'";
					}
				});
			</script>
			
			'.$styles.'
		';	
		
		return $html;
	}
	
	
	/*
	*
	* Prepare custom slider styles as per user configuration
	*
	* @return style/css
	*
	*/
	
	public function prepareCustomStyles(){
		$useImage = $this->getConfig('ajax_catalog/price_slider_conf/use_image');
		
		$handleHeight = $this->getConfig('ajax_catalog/price_slider_conf/handle_height');
		$handleWidth = $this->getConfig('ajax_catalog/price_slider_conf/handle_width');
		
		$sliderHeight = $this->getConfig('ajax_catalog/price_slider_conf/slider_height');
		$sliderWidth = $this->getConfig('ajax_catalog/price_slider_conf/slider_width');
		
		$amountStyle = $this->getConfig('ajax_catalog/price_slider_conf/amount_style');
		
		$bgHandle = '';
		$bgRange = '';
		$bgSlider = '';

		if($useImage){
			$handle = $this->getConfig('ajax_catalog/price_slider_conf/handle_image');
			$range = $this->getConfig('ajax_catalog/price_slider_conf/range_image');
			$slider = $this->getConfig('ajax_catalog/price_slider_conf/background_image');	
			
			if($handle){$bgHandle = 'url('.$this->_imagePath.$handle.') no-repeat';}
			if($range){$bgRange = 'url('.$this->_imagePath.$range.') repeat-x';}
			if($slider){$bgSlider = 'url('.$this->_imagePath.$slider.') repeat-x';}
		}else{	
			$bgHandle = $this->getConfig('ajax_catalog/price_slider_conf/handle_color');
			$bgRange = $this->getConfig('ajax_catalog/price_slider_conf/range_color');
			$bgSlider = $this->getConfig('ajax_catalog/price_slider_conf/background_color');	
			
		}
		
		$html = '<style type="text/css">';	
			$html .= '.ui-slider .ui-slider-handle{';
			if($bgHandle){$html .= 'background:'.$bgHandle.';';}
			$html .= 'width:'.$handleWidth.'px; height:'.$handleHeight.'px; border: 0; margin-top: -1px; cursor: pointer; border-radius: 5px; }';
			
			$html .= '.ui-slider{';
			if($bgSlider){$html .= 'background:'.$bgSlider.';';}
			$html .= ' width:'.$sliderWidth.'px; height:'.$sliderHeight.'px; border:none; border-radius: 0; -moz-border-radius: 0; -webkit-border-radius: 0; cursor: pointer; margin: 5px 5px 20px 8px; }';
			
			$html .= '.ui-slider .ui-slider-range{';
			if($bgRange){$html .= 'background:'.$bgRange.';';}
			$html .= 'border:none; cursor: pointer; box-shadow: inset 0px 1px 2px 0px rgba(0,0,0,.38); }';
			
			$html .= '#amount{'.$amountStyle.'}';	
		$html .= '</style>';		
		return $html;
	}
	
	
	/*
	* Get the Slider config 
	*
	* @return object
	*/
	public function getConfig($key){
		return Mage::getStoreConfig($key);
	}
	
	
	/*
	* Set the Actual Min Price of the search and catalog collection
	*
	* @use category | search collection
	*/
	public function setMinPrice(){
		if( (isset($_GET['q']) && !isset($_GET['min'])) || !isset($_GET['q'])){
			$this->_minPrice = $this->_productCollection->getMinPrice();
			$this->_searchSession->setMinPrice($this->_minPrice);		
		}else{
			$this->_minPrice = $this->_searchSession->getMinPrice();	
		}
	}
	
	/*
	* Set the Actual Max Price of the search and catalog collection
	*
	* @use category | search collection
	*/
	public function setMaxPrice(){
		if( (isset($_GET['q']) && !isset($_GET['max'])) || !isset($_GET['q'])){
			$this->_maxPrice = $this->_productCollection->getMaxPrice();
			$this->_searchSession->setMaxPrice($this->_maxPrice);
		}else{
			$this->_maxPrice = $this->_searchSession->getMaxPrice();
		}
	}
	
	/*
	* Set the Product collection based on the page server to user 
	* Might be a category or search page
	*
	* @set /*
	* Set the Product collection based on the page server to user 
	* Might be a category or search page
	*
	* @set Mage_Catalogsearch_Model_Layer 
	* @set Mage_Catalog_Model_Layer    
	*/
	public function setProductCollection(){
		
		if($this->_currentCategory){
			$this->_productCollection = $this->_currentCategory
							->getProductCollection()
							->addAttributeToSelect('*')
                            ->setOrder('price','desc');
		}else{
			$this->_productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection()	
							->addAttributeToSelect('*');
		}					
	}
	
	
	/*
	* Set Current Max and Min Prices choosed by the user
	*
	* @set price
	*/
	public function setCurrentPrices(){
		
		$this->_currMinPrice = $this->getRequest()->getParam('min');
		$this->_currMaxPrice = $this->getRequest()->getParam('max'); 
	}	
	
	/*
	* Set Current Max and Min Prices choosed by the user
	*
	* @set price
	*/
	public function baseToCurrent($srcPrice){
		$store = $this->getStore();
        return $store->convertPrice($srcPrice, false, false);
	}
	
	
	/*
	* Retrive store object
	*
	* @return object
	*/
	public function getStore(){
		return Mage::app()->getStore();
	}
}
