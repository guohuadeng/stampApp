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
header('Access-Control-Allow-Origin: *');
header('P3P: CP=CAO PSA OUR');
class Sunpop_RestConnect_CatalogController extends Mage_Core_Controller_Front_Action {
	public function categoryTreeAction() {
    $_helper = Mage::helper ( 'catalog/category' );
    $_categories = $_helper->getStoreCategories ();
    $_categorylist = array ();
    if (count ( $_categories ) > 0) {
      foreach ( $_categories as $_category ) {
        $_helper->getCategoryUrl ( $_category );
        $_categorylist [] = array (
            'category_id' => $_category->getId (),
            'name' => $_category->getName (),
            'is_active' => $_category->getIsActive (),
            'position ' => $_category->getPosition (),
            'level ' => $_category->getLevel (),
            'url_key' => Mage::getModel ( 'catalog/category' )->load ( $_category->getId () )->getUrlPath (),
            'thumbnail_url' => Mage::getModel ( 'catalog/category' )->load ( $_category->getId () )->getThumbnailUrl (),
            'image_url' => Mage::getModel ( 'catalog/category' )->load ( $_category->getId () )->getImageUrl (),
            'has_children' => Mage::getModel ( 'catalog/category' )->load ( $_category->getId () )->hasChildren (),
            'product_count' => Mage::getModel ( 'catalog/category' )->load ( $_category->getId () )->getProductCount (),
            'children' => Mage::getModel ( 'catalog/category' )->load ( $_category->getId () )->getAllChildren ()
        );
      }
    }
    echo json_encode ( $_categorylist );
	}

	public function productListCatAction() {
    $categoryid = $this->getRequest ()->getParam ( 'categoryid' );
    $page = ($this->getRequest ()->getParam ( 'page' )) ? ($this->getRequest ()->getParam ( 'page' )) : 1;
    $limit = ($this->getRequest ()->getParam ( 'limit' )) ? ($this->getRequest ()->getParam ( 'limit' )) : 20;
    $order = ($this->getRequest ()->getParam ( 'order' )) ? ($this->getRequest ()->getParam ( 'order' )) : 'position';
    $dir = ($this->getRequest ()->getParam ( 'dir' )) ? ($this->getRequest ()->getParam ( 'dir' )) : 'asc';
    $category = Mage::getModel ( 'catalog/category' )->load ( $categoryid );
    $model = Mage::getModel ( 'catalog/product' ); // getting product model
    $collection = $category->getProductCollection ()->addAttributeToFilter ( 'status', 1 )->addAttributeToFilter ( 'visibility', array (
        'neq' => 1
    ) );
    if($this->getRequest ()->getParam ( 'min' )){
      $collection=$collection->addAttributeToFilter ( 'special_price', array ('gt' => $this->getRequest ()->getParam ( 'min' ) ) );
    }
    if($this->getRequest ()->getParam ( 'max' )){
      $collection=$collection->addAttributeToFilter ( 'special_price', array ('lt' => $this->getRequest ()->getParam ( 'max' ) ) );
    }
    $collection=$collection->addAttributeToSort ( $order, $dir );
    $pages = $collection->setPageSize ( $limit )->getLastPageNumber ();
    // $count=$collection->getSize();
    if ($page <= $pages) {
      $collection->setPage ( $page, $limit );
      $productlist = $this->getProductlist ( $collection, 'catalog' );
    }
    echo json_encode ( $productlist );
	}

	public function productListSpecialAction() {
    // -------------------------------首页 特卖商品 BEGIN------------------------------//
    $order = ($this->getRequest ()->getParam ( 'order' )) ? ($this->getRequest ()->getParam ( 'order' )) : 'entity_id';
    $dir = ($this->getRequest ()->getParam ( 'dir' )) ? ($this->getRequest ()->getParam ( 'dir' )) : 'desc';
    $page = ($this->getRequest ()->getParam ( 'page' )) ? ($this->getRequest ()->getParam ( 'page' )) : 1;
    $limit = ($this->getRequest ()->getParam ( 'limit' )) ? ($this->getRequest ()->getParam ( 'limit' )) : 5;
    $todayDate = Mage::app ()->getLocale ()->date ()->toString ( Varien_Date::DATETIME_INTERNAL_FORMAT );
    $tomorrow = mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + 1, date ( 'y' ) );
    $dateTomorrow = date ( 'm/d/y', $tomorrow );
    // $collection = Mage::getResourceModel ( 'catalog/product_collection' );
    $collection = Mage::getModel ( 'catalog/product' )->getCollection ();
    $collection->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'visibility', array (
        'neq' => 1
    ) )->addAttributeToFilter ( 'status', 1 )->addAttributeToFilter ( 'special_price', array (
        'notnull' => true
    ) )->addAttributeToFilter ( 'special_from_date', array (
        'date' => true,
        'to' => $todayDate
    ) )->addAttributeToFilter ( array (
        array (
            'attribute' => 'special_to_date',
            'date' => true,
            'from' => $dateTomorrow
        ),
        array (
            'attribute' => 'special_to_date',
            'null' => 1
        )
    ) )->addAttributeToSort ( $order, $dir );
    $pages = $collection->setPageSize ( $limit )->getLastPageNumber ();
    // $count=$collection->getSize();
    if ($page <= $pages) {
      $collection->setPage ( $page, $limit );
      $products = $collection->getItems ();
      $productlist = $this->getProductlist ( $products );
    }
    echo json_encode ( $productlist );
    // echo $count;
    // -------------------------------首页 特卖商品 END------------------------------//
	}

	public function productListComingAction() {
    // ------------------------------首页 即将促销商品 BEGIN-------------------------------------//
    $page = ($this->getRequest ()->getParam ( 'page' )) ? ($this->getRequest ()->getParam ( 'page' )) : 1;
    $limit = ($this->getRequest ()->getParam ( 'limit' )) ? ($this->getRequest ()->getParam ( 'limit' )) : 5;
    $order = ($this->getRequest ()->getParam ( 'order' )) ? ($this->getRequest ()->getParam ( 'order' )) : 'entity_id';
    $dir = ($this->getRequest ()->getParam ( 'dir' )) ? ($this->getRequest ()->getParam ( 'dir' )) : 'desc';
    // $todayDate = Mage::app ()->getLocale ()->date ()->toString ( Varien_Date::DATETIME_INTERNAL_FORMAT );
    $tomorrow = mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + 1, date ( 'y' ) );
    $dateTomorrow = date ( 'm/d/y', $tomorrow );
    $tdatomorrow = mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + 3, date ( 'y' ) );
    $tdaTomorrow = date ( 'm/d/y', $tdatomorrow );
    $_productCollection = Mage::getModel ( 'catalog/product' )->getCollection ();
    $_productCollection->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'visibility', array (
        'neq' => 1
    ) )->addAttributeToFilter ( 'status', 1 )->addAttributeToFilter ( 'special_price', array (
        'neq' => 0
    ) )->addAttributeToFilter ( 'special_from_date', array (
        'date' => true,
        'to' => $dateTomorrow
    ) )->addAttributeToFilter ( array (
        array (
            'attribute' => 'special_to_date',
            'date' => true,
            'from' => $tdaTomorrow
        ),
        array (
            'attribute' => 'special_to_date',
            'null' => 1
        )
    ) )->addAttributeToSort ( $order, $dir )/* ->setPage ( $page, $limit ) */;
    $pages = $_productCollection->setPageSize ( $limit )->getLastPageNumber ();
    // $count=$collection->getSize();
    if ($page <= $pages) {
      $_productCollection->setPage ( $page, $limit );
      $products = $_productCollection->getItems ();
      $productlist = $this->getProductlist ( $products );
    }
    echo json_encode ( $productlist );
	}

	public function productListNewAction() {
		// ------------------------------最新商品 BEGIN-------------------------------------//
    $order = ($this->getRequest ()->getParam ( 'order' )) ? ($this->getRequest ()->getParam ( 'order' )) : 'entity_id';
    $dir = ($this->getRequest ()->getParam ( 'dir' )) ? ($this->getRequest ()->getParam ( 'dir' )) : 'desc';                 // ------------------------------首页 预特价商品 BEGIN------------------------------//
    $page = ($this->getRequest ()->getParam ( 'page' )) ? ($this->getRequest ()->getParam ( 'page' )) : 1;
    $limit = ($this->getRequest ()->getParam ( 'limit' )) ? ($this->getRequest ()->getParam ( 'limit' )) : 20;
    $todayDate = Mage::app ()->getLocale ()->date ()->toString ( Varien_Date::DATETIME_INTERNAL_FORMAT );
    $_products = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*')
      ->addAttributeToFilter ( 'status', 1 )->addAttributeToFilter ( 'news_from_date', array (
						'or' => array (
								0 => array (
										'date' => true,
										'to' => $todayDate
								),
								1 => array (
										'is' => new Zend_Db_Expr ( 'null' )
								)
						)
				), 'left' )->addAttributeToFilter ( 'news_to_date', array (
						'or' => array (
								0 => array (
										'date' => true,
										'from' => $todayDate
								),
								1 => array (
										'is' => new Zend_Db_Expr ( 'null' )
								)
						)
				), 'left' )->addAttributeToFilter ( array (
						array (
								'attribute' => 'news_from_date',
								'is' => new Zend_Db_Expr ( 'not null' )
						),
						array (
								'attribute' => 'news_to_date',
								'is' => new Zend_Db_Expr ( 'not null' )
						)
				) )->addAttributeToFilter ( 'visibility', array (
						'in' => array (
								2,
								4
						)
				) )->addAttributeToSort ( 'news_from_date', 'desc' )->addAttributeToSort ( $order, $dir )/* ->setPage ( $page, $limit ) */;
				$pages = $_products->setPageSize ( $limit )->getLastPageNumber ();
				// $count=$collection->getSize();
				if ($page <= $pages) {
					$_products->setPage ( $page, $limit );
					$products = $_products->getItems ();
					$productlist = $this->getProductlist ( $products );
				}
				echo json_encode ( $productlist );
				// ------------------------------首页 最新商品 END--------------------------------//
	}

	public function getProductlist($products, $mod = 'product') {
		$productlist = array ();
		$baseCurrency = Mage::app ()->getStore ()->getBaseCurrency ()->getCode ();
		$currentCurrency = Mage::app ()->getStore ()->getCurrentCurrencyCode ();
		foreach ( $products as $product ) {
			if ($mod == 'catalog') {
				$product = Mage::getModel ( 'catalog/product' )->load ( $product ['entity_id'] );
			}

/*  if ($product->getId () == '1')
    return var_dump(get_class_methods($product));*/

			$regular_price_with_tax = number_format ( Mage::helper ( 'directory' )->currencyConvert ( $product->getPrice (), $baseCurrency, $currentCurrency ), 2, '.', '' );
			$final_price_with_tax = $product->getSpecialPrice ();
			if (!is_null($final_price_with_tax))	{
				$final_price_with_tax = number_format ( Mage::helper ( 'directory' )->currencyConvert ( $product->getSpecialPrice (), $baseCurrency, $currentCurrency ), 2, '.', '' );
				$discount = round (($regular_price_with_tax - $final_price_with_tax)/$regular_price_with_tax*100);
				$discount = $discount.'%';
				}
			else {
				$discount = null;
			}
			$productlist [] = array (
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
							//also use getSmallImage() or getThumbnail()
					'url_key' => $product->getProductUrl (),
					'regular_price_with_tax' => $regular_price_with_tax,
					'final_price_with_tax' => $final_price_with_tax,
					'discount' => $discount,
					'symbol'=> Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol()
			);
		}
		return $productlist;
	}
}
