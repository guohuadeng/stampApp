<?php

if (!Mage::getStoreConfig('megamenu/general/enabled') ||
   (Mage::getStoreConfig('megamenu/general/ie6_ignore') && Mage::helper('megamenu')->isIE6()))
{
    class Smartwave_Megamenu_Block_Topmenu extends Mage_Page_Block_Html_Topmenu
    {

    }
} else {

    class Smartwave_Megamenu_Block_Topmenu extends Smartwave_Megamenu_Block_Navigation
    {

    }
}
