<?php
/*
 * Copyright (C) 2012 Clearspring Technologies, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
<?php

class AddThis_SharingTool_Model_Source_Buttons
{
    public function toOptionArray()
    { 
    	$result = array();
    	
        $result[] = array('class'=>'buttons','value'=>'style_1','label'=>'&nbsp;&nbsp;
		<img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/facebook.png" style="vertical-align:middle"/>&nbsp;
		<img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/twitter.png" style="vertical-align:middle"/>&nbsp;
		<img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/email.png" style="vertical-align:middle"/>&nbsp;
		<img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/google.png" style="vertical-align:middle"/>&nbsp;
		<img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/linkedin.png" style="vertical-align:middle"/>&nbsp;
		<img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/menu.png" style="vertical-align:middle"/><br/><br/>');
        
		$result[] = array('value'=>'style_2','label'=>'&nbsp;&nbsp;<img src="https://cache.addthiscdn.com/downloads/plugins/magento/gtc-like-tweet-share.gif" style="vertical-align:middle;width:273px;"/><br/><br/>');
		
		$result[] = array('value'=>'style_3','label'=>'&nbsp;&nbsp;<img src="https://cache.addthiscdn.com/icons/v2/thumbs/32x32/facebook.png" style="vertical-align:middle"/>&nbsp;
	                <img src="https://cache.addthiscdn.com/icons/v2/thumbs/32x32/twitter.png" style="vertical-align:middle" />&nbsp;
	                <img src="https://cache.addthiscdn.com/icons/v2/thumbs/32x32/email.png" style="vertical-align:middle" />&nbsp;
		            <img src="https://cache.addthiscdn.com/icons/v2/thumbs/32x32/google.png" style="vertical-align:middle" />&nbsp;
		            <img src="https://cache.addthiscdn.com/icons/v2/thumbs/32x32/linkedin.png" style="vertical-align:middle" />&nbsp;
		            <img src="https://cache.addthiscdn.com/icons/v2/thumbs/32x32/addthis.png" style="vertical-align:middle" /><br/><br/>');
	   
	    $result[] = array('value'=>'style_4','label'=>'&nbsp;&nbsp;<img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/menu.png" style="vertical-align:middle"/>&nbsp;
    			   <label>Share</label>&nbsp;<img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/facebook.png" style="vertical-align:middle" />&nbsp;
    			   <img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/myspace.png" style="vertical-align:middle" />&nbsp;
    			   <img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/google.png" style="vertical-align:middle" />&nbsp;
                   <img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/twitter.png" style="vertical-align:middle" /><br/><br/>');		
		
	    $result[] = array('value'=>'style_5','label'=>'&nbsp;&nbsp;<img src="https://cache.addthiscdn.com/icons/v2/thumbs/16x16/menu.png" style="vertical-align:middle"/>&nbsp;
    			   <label>Share</label><br/><br/>');		
	   
	   
	    $result[] = array('value'=>'style_6','label'=>'&nbsp;&nbsp;<img src="https://cache.addthiscdn.com/www/20111123101657/images/sharecount-horizontal.gif" style="vertical-align:middle"/><br/><br/>');	
	   
	    $result[] = array('value'=>'style_7','label'=>'&nbsp;&nbsp;<img src="https://cache.addthiscdn.com/www/20111123101657/images/sharecount-vertical.gif" style="vertical-align:middle"/><br/><br/>');
	   
	    $result[] = array('value'=>'style_8','label'=>'&nbsp;&nbsp;<img src="https://s7.addthis.com/static/btn/sm-plus.gif" style="vertical-align:middle"/><br/><br/>');
	   
	    $result[] = array('value'=>'style_9','label'=>'&nbsp;&nbsp;<b>Custom Button</b><style>#sharing_tool_button_style_button_setstyle_1{margin-left:6px;} .note{width:500px;}</style><br/><br/>');
	    
	    $result[] = array('value'=>'style_10','label'=>'&nbsp;&nbsp;<b>Custom Code</b>');
	   
		return $result;
    }
}