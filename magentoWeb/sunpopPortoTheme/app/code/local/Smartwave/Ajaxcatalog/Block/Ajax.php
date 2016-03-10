<?php 

class Smartwave_Ajaxcatalog_Block_Ajax extends Mage_Core_Block_Template{
	public function __construct(){
		
		$this->config = Mage::getStoreConfig('ajax_catalog');
		$this->url = Mage::getStoreConfig('web/unsecure/base_url');
		
		$this->ajaxSlider = $this->config['price_slider_settings']['slider'];
		$this->ajaxLayered = $this->config['price_slider_settings']['layered'];
		$this->ajaxToolbar = $this->config['price_slider_settings']['toolbar'];
        $this->infiniteScroll = $this->config['price_slider_settings']['infinitescroll'];
		$this->overlayColor = $this->config['ajax_conf']['overlay_color'];
		$this->overlayOpacity = $this->config['ajax_conf']['overlay_opacity'];
		$this->loadingText = $this->config['ajax_conf']['loading_text'];
		$this->loadingTextColor = $this->config['ajax_conf']['loading_text_color'];
        if(isset($this->config['ajax_conf']['loading_image']) && $this->config['ajax_conf']['loading_image']){
            $this->loadingImage = $this->config['ajax_conf']['loading_image'];
        }
		if($this->loadingImage == '' || $this->loadingImage == null){
			$this->loadingImage = "";
		}else{
			$this->loadingImage = $this->url.'media/smartwave/ajaxcatalog/'.$loadingImage;
		}	
	}
	
	public function getCallbackJs(){
		return Mage::getStoreConfig('ajax_catalog/ajax_conf/afterAjax');
	}
}