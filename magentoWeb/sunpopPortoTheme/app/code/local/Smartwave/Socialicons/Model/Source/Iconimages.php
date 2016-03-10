<?php

class Smartwave_Socialicons_Model_Source_Iconimages
{
    public function toOptionArray()
    { 
    	$result = array();
    	
        $result[] = array('value'=>'style_1','label'=>'&nbsp;&nbsp;
		<img src="'.Mage::getBaseUrl('skin')."frontend/smartwave/default/socialicons/images/social-icons-sprite.png".'" style="vertical-align:middle;background-color: #000;"/><br/><br/>');
		return $result;
    }
}