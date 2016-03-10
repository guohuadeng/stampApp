<?php

if (!class_exists('Smartwave_Blog_Block_Product_ToolbarCommon')) {
    if (Mage::helper('blog')->isMobileInstalled()) {
        class Smartwave_Blog_Block_Product_ToolbarCommon extends Smartwave_Mobile_Block_Catalog_Product_List_Toolbar
        {
        }
    } else {
        class Smartwave_Blog_Block_Product_ToolbarCommon extends Mage_Catalog_Block_Product_List_Toolbar
        {
        }
    }
}