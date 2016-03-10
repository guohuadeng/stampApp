<?php

class Smartwave_Porto_Block_Adminhtml_System_Config_Form_Field_Texture extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $element->getElementHtml();
		$jsUrl = $this->getJsUrl('smartwave/jquery/jquery-1.8.3.min.js');
		$textureUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . Mage::helper('porto')->getTexturePath();
		
		$bgcPickerId = str_replace('_texture', '_bg_color', $element->getHtmlId());
		
		$previewId = $element->getHtmlId() . '-texture-preview';
		
		if (Mage::registry('jqueryLoaded') == false)
		{
			$html .= '<script type="text/javascript" src="'. $jsUrl .'"></script>
			    <script type="text/javascript">jQuery.noConflict();</script>';
			Mage::register('jqueryLoaded', 1);
        }

	    $html .= '<br/><div id="'. $previewId .'" style="width:280px; height:160px; margin:10px 0; background-color:transparent;"></div>
		    <script type="text/javascript">
			    jQuery(function($){
				    var texture		= $("#'. $element->getHtmlId()	.'");
				    var bgcolor		= $("#'. $bgcPickerId			.'");
				    var preview 	= $("#'. $previewId				.'");
				    
				    preview.css("background-color", bgcolor.attr("value"));
				    texture.change(function() {
                        var bg_image = "url('. $textureUrl .'" + texture.val() + ".png)";
                        if(texture.val() == 0)
                            bg_image = "none";
					    preview.css({
						    "background-color": bgcolor.css("background-color"),
						    "background-image": bg_image
					    });
				    }).change();
                    bgcolor.change(function(){texture.change();});
			    });
		    </script>';
		
        return $html;
    }
}
