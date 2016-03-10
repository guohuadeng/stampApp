<?php
class Smartwave_Porto_Block_Configurableswatches_Catalog_Media_Js_Product extends Mage_ConfigurableSwatches_Block_Catalog_Media_Js_Product {
    public function getProductImageFallbacks($keepFrame = null) {
        /* @var $helper Mage_ConfigurableSwatches_Helper_Mediafallback */
        $helper = Mage::helper('porto/mediafallback');

        $fallbacks = array();
		$store = Mage::app()->getStore();
		$code  = $store->getCode();

        $products = $this->getProducts();

		$keepFrame = Mage::getStoreConfig("porto_settings/product_view/aspect_ratio",$code);
		$ratio_width = Mage::getStoreConfig("porto_settings/product_view/ratio_width",$code);
		$ratio_height = Mage::getStoreConfig("porto_settings/product_view/ratio_height",$code);
        /* @var $product Mage_Catalog_Model_Product */
        foreach ($products as $product) {
            if($keepFrame)
	            $imageFallback = $helper->getConfigurableImagesFallbackArray($product, $this->_getImageSizes(), $keepFrame, $ratio_width);
			else
				$imageFallback = $helper->getConfigurableImagesFallbackArray($product, $this->_getImageSizes(), $keepFrame, $ratio_width, $ratio_height);
            $fallbacks[$product->getId()] = array(
                'product' => $product,
                'image_fallback' => $this->_getJsImageFallbackString($imageFallback)
            );
        }

        return $fallbacks;
    }
}
?>