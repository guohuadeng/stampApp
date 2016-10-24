<?php
/**
 * * NOTICE OF LICENSE
 * * This source file is subject to the Open Software License (OSL 3.0)
 *
 * Author: Ivan Deng
 * QQ: 300883
 * Email: 300883@qq.com
 * @copyright  Copyright (c) 2008-2015 Sunpop Ltd. (http://www.sunpop.cn)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
header("Access-Control-Allow-Origin: *");
header("P3P: CP=CAO PSA OUR");
class Sunpop_RestConnect_ProductsController extends Mage_Core_Controller_Front_Action {
	public function getcustomoptionAction() {
		$baseCurrency = Mage::app ()->getStore ()->getBaseCurrency ()->getCode ();
		$currentCurrency = Mage::app ()->getStore ()->getCurrentCurrencyCode ();
		$productid = $this->getRequest ()->getParam ( 'productid' );
		$product = Mage::getModel ( "catalog/product" )->load ( $productid );
		$selectid = 1;
		$select = array ();
		foreach ( $product->getOptions () as $o ) {
			if ($o->getType () == "field") {
				$select [$selectid] = array (
						'option_id' => $o->getId (),
						'custom_option_type' => $o->getType (),
						'custom_option_title' => $o->getTitle (),
						'is_require' => $o->getIsRequire (),
						'price' => number_format ( Mage::helper ( 'directory' )->currencyConvert ( $o->getPrice (), $baseCurrency, $currentCurrency ), 2, '.', '' ),
						'price_type' => $o->getPriceType (),
						'sku' => $o->getSku (),
						'max_characters' => $o->getMaxCharacters ()
				);
			} elseif ($o->getType () == "file") {
		    //$ofile =  Mage::getModel ( "catalog/product_option_type_file" );
			  //var_dump(get_class_methods($ofileview));
			  //return;
				$select [$selectid] = array (
						'option_id' => $o->getId (),
						'custom_option_type' => $o->getType (),
						'custom_option_title' => $o->getTitle (),
						'is_require' => $o->getIsRequire (),
						'price' => number_format ( Mage::helper ( 'directory' )->currencyConvert ( $o->getPrice (), $baseCurrency, $currentCurrency ), 2, '.', '' ),
						'price_type' => $o->getPriceType (),
						'sku' => $o->getSku (),
						'max_characters' => $o->getMaxCharacters (),
						'file_extension' => $o->getFileExtension (),
						'image_sizex' => $o->getImageSizeX (),
						'image_sizey' => $o->getImageSizeY ()
				);
			} else {
				$max_characters = $o->getMaxCharacters ();
				$optionid = 1;
				$options = array ();
				$values = $o->getValues ();
				foreach ( $values as $v ) {
					$options [$optionid] = $v->getData ();
					if(null!==$v->getData('price') && null!==$v->getData('default_price')){
						$options [$optionid]['price']=number_format ( Mage::helper ( 'directory' )->currencyConvert ( $v->getPrice (), $baseCurrency, $currentCurrency ), 2, '.', '' );
						$options [$optionid]['default_price']=number_format ( Mage::helper ( 'directory' )->currencyConvert ( $v->getDefaultPrice (), $baseCurrency, $currentCurrency ), 2, '.', '' );
					}


					$optionid ++;
				}
				$select [$selectid] = array (
						'option_id' => $o->getId (),
						'custom_option_type' => $o->getType (),
						'custom_option_title' => $o->getTitle (),
						'is_require' => $o->getIsRequire (),
						'price' => number_format ( Mage::helper ( 'directory' )->currencyConvert ( $o->getFormatedPrice (), $baseCurrency, $currentCurrency ), 2, '.', '' ),
						'max_characters' => $max_characters,
						'custom_option_value' => $options
				);
			}

			$selectid ++;
			// echo "----------------------------------<br/>";
		}
		echo json_encode ( $select );
	}
	public function getproductdetailAction() {
		Mage::app ()->cleanCache ();
		$productdetail = array ();
		$baseCurrency = Mage::app ()->getStore ()->getBaseCurrency ()->getCode ();
		$currentCurrency = Mage::app ()->getStore ()->getCurrentCurrencyCode ();
		$productid = $this->getRequest ()->getParam ( 'productid' );
		$product = Mage::getModel ( "catalog/product" )->load ( $productid );

		$storeUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		//$description =  nl2br ( $product->getDescription () );
		$description =   $product->getDescription ();
		$description = str_replace("{{media url=\"",$storeUrl,$description);
		$description = str_replace("\"}}","",$description);

		if ($product->getOptions ())
			$has_custom_options = true;
		else
			$has_custom_options = false;
		$addtionatt=$this->getAdditional();
		$regular_price_with_tax = number_format ( Mage::helper ( 'directory' )->currencyConvert ( $product->getPrice (), $baseCurrency, $currentCurrency ), 2, '.', '' );
		$final_price_with_tax = $product->getSpecialPrice ();
		if (!is_null($final_price_with_tax))	{
			$final_price_with_tax = number_format ( Mage::helper ( 'directory' )->currencyConvert ( $product->getSpecialPrice (), $baseCurrency, $currentCurrency ), 2, '.', '' );
			$discount = round (($regular_price_with_tax - $final_price_with_tax)/$regular_price_with_tax*100);
			$discount = $discount.'%';
			}
		$productdetail = array (
				'entity_id' => $product->getId (),
				'sku' => $product->getSku (),
				'name' => $product->getName (),
				'news_from_date' => $product->getNewsFromDate (),
				'news_to_date' => $product->getNewsToDate (),
				'special_from_date' => $product->getSpecialFromDate (),
				'special_to_date' => $product->getSpecialToDate (),
				'image_url' => $product->getImageUrl (),
				'image_thumbnail_url' => Mage::getModel ( 'catalog/product_media_config' )->getMediaUrl( $product->getThumbnail() ),
				'image_small_url' => Mage::getModel ( 'catalog/product_media_config' )->getMediaUrl( $product->getSmallImage() ),
				'url_key' => $product->getProductUrl (),
				'is_in_stock' => $product->isAvailable (),
				'has_custom_options' => $has_custom_options,
				'regular_price_with_tax' => $regular_price_with_tax,
				'final_price_with_tax' => $final_price_with_tax,
				'discount' => $discount,
				'storeUrl' => $storeUrl,
				'symbol' => Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol () ,
				'weight'=>number_format($product->getWeight()),
				'additional'=>$addtionatt,
				'description' => $description
		);
		echo json_encode ( $productdetail );
	}
	public function getPicListsAction() {
		$productId = ( int ) $this->getRequest ()->getParam ( 'product' );
		$_product = Mage::getModel ( "catalog/product" )->load ( $productid );
		$_images = Mage::getModel ( 'catalog/product' )->load ( $productId )->getMediaGalleryImages ();
		$images = array ();
		foreach ( $_images as $_image ) {
			$images [] = array (
					'url' => $_image->getUrl (),
					'position' => $_image->getPosition ()
			);
		}
		echo json_encode ( $images );
	}
	public function getAdditional(array $excludeAttr = array()) {
		$data = array ();
		$productId = ( int ) $this->getRequest ()->getParam ( 'productid' );
		$product = Mage::getModel('catalog/product')->load($productId);
		$attributes = $product->getAttributes ();

		foreach ( $attributes as $attribute ) {
            $value= $attribute->getFrontend()->getValue($product);
			$code = $attribute->getAttributeCode();
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                if (!$product->hasData($attribute->getAttributeCode())) {
                    $value = 'N/A';
                } elseif ((string)$value == '') {
                    $value = 'N/A';
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = Mage::app()->getStore()->convertPrice($value, true);
                }
             if ($attribute->usesSource() && $value == $this->__('No'))	{
					$value = 'N/A';
				}

                if (is_string($value) && strlen($value)) {
                    $data[$code] = array(
                        'label' => $attribute->getStoreLabel(),
                        'type' =>  $attribute->getBackend()->getType(),
                        'value' => $value,
                        'code'  => $code
                    );
                }
            }
        }
        return $data;
	}
	public function testAction(array $excludeAttr = array()) {
		$data = array ();
		$productId = ( int ) $this->getRequest ()->getParam ( 'productid' );
		$product = Mage::getModel('catalog/product')->load($productId);
		$attributes = $product->getAttributes ();

		foreach ( $attributes as $attribute ) {
            $value= $attribute->getFrontend()->getValue($product);
			$code = $attribute->getAttributeCode();
			var_dump(get_class_methods($attribute));
			break;
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                if (!$product->hasData($attribute->getAttributeCode())) {
                    $value = 'N/A';
                } elseif ((string)$value == '') {
                    $value = 'N/A';
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = Mage::app()->getStore()->convertPrice($value, true);
                }

                if (is_string($value) && strlen($value)) {
                    $data[$code] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'code'  => $code
                    );
                }
            }
        }
        return $data;
	}
}
