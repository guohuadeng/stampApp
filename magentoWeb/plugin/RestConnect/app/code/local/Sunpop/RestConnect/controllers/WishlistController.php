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
class Sunpop_RestConnect_WishlistController extends Mage_Core_Controller_Front_Action {
	protected $_optionsCfg = array('default' => array(
        'helper' => 'catalog/product_configuration',
        'template' => 'wishlist/options_list.phtml'
    ));
	public function addToWishlistAction() {
		$response = array ();
		if (! Mage::getStoreConfigFlag ( 'wishlist/general/active' )) {
			$response ['status'] = 'ERROR';
			$response ['message'] = $this->__ ( 'Wishlist Has Been Disabled By Admin' );
		}
		if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
			$response ['status'] = 'ERROR';
			$response ['message'] = $this->__ ( 'Please Login First' );
		}

		if (empty ( $response )) {
			$session = Mage::getSingleton ( 'customer/session' );
			$wishlist = $this->_getWishlist ();
			if (! $wishlist) {
				$response ['status'] = 'ERROR';
				$response ['message'] = $this->__ ( 'Unable to Create Wishlist' );
			} else {

				$productId = ( int ) $this->getRequest ()->getParam ( 'product' );
				if (! $productId) {
					$response ['status'] = 'ERROR';
					$response ['message'] = $this->__ ( 'Product Not Found' );
				} else {

					$product = Mage::getModel ( 'catalog/product' )->load ( $productId );
					if (! $product->getId () || ! $product->isVisibleInCatalog ()) {
						$response ['status'] = 'ERROR';
						$response ['message'] = $this->__ ( 'Cannot specify product.' );
					} else {

						try {
							$requestParams = $this->getRequest ()->getParams ();
							$buyRequest = new Varien_Object ( $requestParams );

							$result = $wishlist->addNewItem ( $product, $buyRequest );
							if (is_string ( $result )) {
								Mage::throwException ( $result );
							}
							$wishlist->save ();

							Mage::dispatchEvent ( 'wishlist_add_product', array (
									'wishlist' => $wishlist,
									'product' => $product,
									'item' => $result
							) );

							Mage::helper ( 'wishlist' )->calculate ();

							$message = $this->__ ( '%1$s has been added to your wishlist.', $product->getName (), $referer );
							$response ['status'] = 'SUCCESS';
							$response ['message'] = $message;

							Mage::unregister ( 'wishlist' );
						} catch ( Mage_Core_Exception $e ) {
							$response ['status'] = 'ERROR';
							$response ['message'] = $this->__ ( 'An error occurred while adding item to wishlist: %s', $e->getMessage () );
						} catch ( Exception $e ) {
							mage::log ( $e->getMessage () );
							$response ['status'] = 'ERROR';
							$response ['message'] = $this->__ ( 'An error occurred while adding item to wishlist.' );
						}
					}
				}
			}
		}
		// echo $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $response ) );
		echo json_encode ( $response );
		return;
	}
	public function getWishlistAction() {
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
			echo json_encode ( array (
					'status' => 'ERROR',
					'message' => 'customer_not_login'
			));
			return ;
        }
		$result = $this->_getWishlistInfo();
		$result = Mage::helper('core')->jsonEncode($result);
		$this->getResponse()->setBody(urldecode($result));
	}

	protected function _getWishlistInfo() {
		$wishlist = Mage::registry ( 'wishlist' );
		$baseCurrency = Mage::app ()->getStore ()->getBaseCurrency ()->getCode ();
		$currentCurrency = Mage::app ()->getStore ()->getCurrentCurrencyCode ();
		if ($wishlist) {
			return $wishlist;
		}

		try {
			$wishlist = Mage::getModel ( 'wishlist/wishlist' )->loadByCustomer ( Mage::getSingleton ( 'customer/session' )->getCustomer (), true );
			Mage::register ( 'wishlist', $wishlist );
		} catch ( Mage_Core_Exception $e ) {
			Mage::getSingleton ( 'wishlist/session' )->addError ( $e->getMessage () );
		} catch ( Exception $e ) {
			Mage::getSingleton ( 'wishlist/session' )->addException ( $e, Mage::helper ( 'wishlist' )->__ ( 'Cannot create wishlist.' ) );
			return false;
		}
		$items = array ();
		foreach ( $wishlist->getItemCollection () as $item ) {


			$data = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
			if (empty($data['helper'])
				|| !Mage::helper($data['helper']) instanceof Mage_Catalog_Helper_Product_Configuration_Interface
			) {
				Mage::throwException($this->__("Helper for wishlist options rendering doesn't implement required interface."));
			}

			$option = Mage::helper($data['helper'])->getOptions($item);
			$cuoptions = array();
			if(!empty($option)){
				foreach($option as $i => $option){
					foreach($option as $index=>$p){
						$cuoptions[$i][$index] = urlencode($p);
					}
				}
			}

			$product = Mage::getModel ( 'catalog/product' )->setStoreId ( $item->getStoreId () )->load ( $item->getProductId () );
			if ($product->getId ()) {
				$name = $product->getName();
				$items [] = array (
						'name' => urlencode($name),
						'entity_id' => $product->getId (),
						'regular_price_with_tax' => number_format ( Mage::helper ( 'directory' )->currencyConvert ( $product->getPrice (), $baseCurrency, $currentCurrency ), 2, '.', '' ),
						'final_price_with_tax' => number_format ( Mage::helper ( 'directory' )->currencyConvert ( $product->getSpecialPrice (), $baseCurrency, $currentCurrency ), 2, '.', '' ),
						'sku' => $product->getSku () ,
						'symbol' => Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol (),
            'image_url' => $product->getImageUrl (),
            'image_thumbnail_url' => Mage::getModel ( 'catalog/product_media_config' )->getMediaUrl( $product->getThumbnail() ),
            'image_small_url' => Mage::getModel ( 'catalog/product_media_config' )->getMediaUrl( $product->getSmallImage() ),
						'option' => $cuoptions
				);
			}
		}
		return array (
				'wishlist' => $wishlist->getData (),
				'items' => $items
		);
	}
	public function getOptionsRenderCfg($productType)
    {
        if (isset($this->_optionsCfg[$productType])) {
            return $this->_optionsCfg[$productType];
        } elseif (isset($this->_optionsCfg['default'])) {
            return $this->_optionsCfg['default'];
        } else {
            return null;
        }
    }

	protected function _getWishlist() {
		$wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist');
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                Mage::throwException(
                    Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
                );
            }

            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            return false;
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
                Mage::helper('wishlist')->__('Wishlist could not be created.')
            );
            return false;
        }
		return $wishlist;
	}

	public function updateItemOptionsAction(){
		$session = Mage::getSingleton('customer/session');
        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            echo json_encode ( array (
					'status' => 'ERROR',
					'message' => 'prodcut_not_exists'
			));
			return ;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
			echo json_encode ( array (
					'status' => 'ERROR',
					'message' => 'Cannot specify product.'
			));
			return ;
        }

        try {
            $id = (int) $this->getRequest()->getParam('id');
            /* @var Mage_Wishlist_Model_Item */
            $item = Mage::getModel('wishlist/item');
            $item->load($id);
            $wishlist = $this->_getWishlist($item->getWishlistId());
            if (!$wishlist) {
                echo json_encode ( array (
					'status' => 'ERROR',
					'message' => 'wishlist_not_exists.'
				));
				return ;
            }

            $buyRequest = new Varien_Object($this->getRequest()->getParams());

            $wishlist->updateItem($id, $buyRequest)
                ->save();

            Mage::helper('wishlist')->calculate();
            Mage::dispatchEvent('wishlist_update_item', array(
                'wishlist' => $wishlist, 'product' => $product, 'item' => $wishlist->getItem($id))
            );

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been updated in your wishlist.', $product->getName());
            echo json_encode ( array (
					'status' => 'SUCCESS',
					'message' => $message
			));
			return ;
        } catch (Mage_Core_Exception $e) {
			 echo json_encode ( array (
					'status' => 'ERROR',
					'message' => $e->getMessage()
			));
			return ;
        } catch (Exception $e) {
			 echo json_encode ( array (
					'status' => 'ERROR',
					'message' => $this->__('An error occurred while updating wishlist.')
			));
			return ;
            $session->addError($this->__('An error occurred while updating wishlist.'));
            Mage::logException($e);
        }
        //$this->_redirect('*/*', array('wishlist_id' => $wishlist->getId()));
	}

	public function removeAction()
    {
        $id = (int) $this->getRequest()->getParam('item');
        $item = Mage::getModel('wishlist/item')->load($id);
        if (!$item->getId()) {
            echo json_encode ( array (
					'status' => 'ERROR',
					'message' => 'item_not_exists.'
				));
			return ;
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            echo json_encode ( array (
					'status' => 'ERROR',
					'message' => 'wishlist_not_exists.'
				));
			return ;
        }
        try {
            $item->delete();
            $wishlist->save();
			echo json_encode ( array (
					'status' => 'SUCCESS',
					'message' => 'delete successfully'
			));
			return ;
        } catch (Mage_Core_Exception $e) {
			 echo json_encode ( array (
					'status' => 'ERROR',
					'message' => $this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage())
				));
			return ;
        } catch (Exception $e) {
			echo json_encode ( array (
					'status' => 'ERROR',
					'message' => $this->__('An error occurred while deleting the item from wishlist.')
				));
			return ;
        }

        Mage::helper('wishlist')->calculate();

        $this->_redirectReferer(Mage::getUrl('*/*'));
    }
}
