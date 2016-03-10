<?php
/*
 * Copyright (C) 2012 Clearspring Technologies, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
<?php

class AddThis_SmartLayers_Block_Layer extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('addthis/smartlayers/smartlayers.phtml');
    }
    
    /* General */ 
    public function getPluginEnabledStatus(){
    	
    	return Mage::getStoreConfig('smart_layers/general/enabled');
    }
    
    public function getPubId(){
    	 
    	return Mage::getStoreConfig('plugins_general/general/pubid');
    }

    public function getTheme(){
    	 
    	return Mage::getStoreConfig('smart_layers/general/theme');
    }
    
    /* Follow */
    public function getFollow(){
    
    	$follow_services = array();
    	
    	$follow_services['facebook'] = Mage::getStoreConfig('smart_layers/follow/facebook');
    	$follow_services['twitter'] = Mage::getStoreConfig('smart_layers/follow/twitter');
    	$follow_services['linkedin'] = Mage::getStoreConfig('smart_layers/follow/linkedin');
    	$follow_services['linkedin_comp'] = Mage::getStoreConfig('smart_layers/follow/linkedin_comp');
    	$follow_services['google'] = Mage::getStoreConfig('smart_layers/follow/google');
    	$follow_services['youtube'] = Mage::getStoreConfig('smart_layers/follow/youtube');
    	$follow_services['flickr'] = Mage::getStoreConfig('smart_layers/follow/flickr');
    	$follow_services['vimeo'] = Mage::getStoreConfig('smart_layers/follow/vimeo');
    	$follow_services['pinterest'] = Mage::getStoreConfig('smart_layers/follow/pinterest');
    	$follow_services['instagram'] = Mage::getStoreConfig('smart_layers/follow/instagram');
    	$follow_services['foursquare'] = Mage::getStoreConfig('smart_layers/follow/foursquare');
    	$follow_services['tumblr'] = Mage::getStoreConfig('smart_layers/follow/tumblr');
    	$follow_services['rss'] = Mage::getStoreConfig('smart_layers/follow/rss');
    	
    	return $follow_services;
    }
    
    /* Share */    
    public function getShareEnabled(){
    
    	return Mage::getStoreConfig('smart_layers/share/enabled');
    }
    
    public function getShareButtonPosition(){
    
    	return Mage::getStoreConfig('smart_layers/share/share_button_position');
    }   
    
    public function getShareButtonCount(){
    
    	return Mage::getStoreConfig('smart_layers/share/share_button_count');
    }
    
    /* What's Next */    
    public function getWhatsNextEnabled(){
    
    	return Mage::getStoreConfig('smart_layers/whatsnext/enabled');
    }
    
    /* Recomended Content */    
    public function getRecomendedEnabled(){
    	 
    	return Mage::getStoreConfig('smart_layers/recommended/enabled');
    }
    
    public function getRecomendedHeader(){
    	 
    	return Mage::getStoreConfig('smart_layers/recommended/recommended_header');
    }
    
    /*Custom Code*/
    public function getCustomCodeEnabled(){
    	 
    	return Mage::getStoreConfig('smart_layers/custom_code/enabled');
    } 
    
    public function getCustomCode(){
    	 
    	return Mage::getStoreConfig('smart_layers/custom_code/content');
    }     
}
