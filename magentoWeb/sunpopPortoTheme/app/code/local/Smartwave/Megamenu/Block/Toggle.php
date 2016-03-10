<?php

class Smartwave_Megamenu_Block_Toggle extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        if (!Mage::getStoreConfig('megamenu/general/enable')) return;
        $layout = $this->getLayout();
        $topnav = $layout->getBlock('catalog.topnav');        
        if (is_object($topnav)) {
            $topnav->setTemplate('smartwave/megamenu/html/topmenu.phtml');            
            $head = $layout->getBlock('head');
            $head->addItem('skin_js', 'megamenu/js/megamenu.js');                  
            $head->addItem('skin_css', 'megamenu/css/font-awesome.min.css');  
            $head->addItem('skin_css', 'megamenu/css/megamenu.css');  
            $head->addItem('skin_css', 'megamenu/css/megamenu_responsive.css');
        }
    }   
}
