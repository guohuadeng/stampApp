<?php

class Smartwave_Porto_Helper_Richsnippets extends Mage_Core_Helper_Abstract
{
	const SCHEMA_PRODUCT			= 'itemscope itemtype="http://schema.org/Product"';
	const SCHEMA_OFFER				= 'itemprop="offers" itemscope itemtype="http://schema.org/Offer"';
	const SCHEMA_OFFER_AGGREGATE	= 'itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer"';

	protected $_aggregateOffer = false;

	public function isEnabled() {
		return Mage::getStoreConfig('porto_settings/richsnippets/enable');
	}

	public function getPriceProperties($product) {
		$productTypeId = $product->getTypeId();
		if ($productTypeId === 'grouped'){
			return '';
		}

		$includeTax = Mage::getStoreConfig('porto_settings/richsnippets/price_incl_tax');
		$html = '<meta itemprop="priceCurrency" content="' . Mage::app()->getStore()->getCurrentCurrencyCode() . '" />';

		if ($productTypeId === 'bundle') {
			if ($product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
				$minimalPrice = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), $includeTax);
				$html .= '<meta itemprop="price" content="' . $minimalPrice . '" />';
			} else {
				$price_model = $product->getPriceModel();

				list($minimalPrice, $maximalPrice) = $price_model->getPricesDependingOnTax($product, null, $includeTax);

				if ($product->getPriceView()) {
					$html .= '<meta itemprop="price" content="' . $minimalPrice . '" />';
				} else {
					$this->_aggregateOffer = true;
					$html .= '<meta itemprop="lowPrice" content="' . $minimalPrice . '" />';
					$html .= '<meta itemprop="highPrice" content="' . $maximalPrice . '" />';
				}
			}
		} else {
			$html .= '<meta itemprop="price" content="' . Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), $includeTax) . '" />';
		}

		return $html;
	}

	public function getOfferItemscope() {
		if ($this->_aggregateOffer) {
			return self::SCHEMA_OFFER_AGGREGATE;
		} else {
			return self::SCHEMA_OFFER;
		}
	}

	public function getProductItemscope() {
		return self::SCHEMA_PRODUCT;
	}
}