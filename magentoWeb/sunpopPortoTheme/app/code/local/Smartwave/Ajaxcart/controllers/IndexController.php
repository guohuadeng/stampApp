<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Smartwave_Ajaxcart_IndexController extends Mage_Checkout_CartController
{
	public function addAction()
	{
		$cart   = $this->_getCart();
		$params = $this->getRequest()->getParams();
		if($params['isAjax'] == 1){
			$response = array();
			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
					array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$product = $this->_initProduct();
				$related = $this->getRequest()->getParam('related_product');

				/**
				 * Check product availability
				 */
				if (!$product) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Unable to find Product ID');
				}

				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}
                $cart->getQuote()->setTotalsCollectedFlag(false);
				$cart->save();

				$this->_getSession()->setCartWasUpdated(true);

				/**
				 * @todo remove wishlist observer processAddToCart
				 */
				Mage::dispatchEvent('checkout_cart_add_product_complete',
				array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);

				if (!$cart->getQuote()->getHasError()){
                    $store = Mage::app()->getStore();
                    $code  = $store->getCode();
                    $aspect_ratio = Mage::getStoreConfig("porto_settings/category/aspect_ratio",$code);
                    $ratio_width = Mage::getStoreConfig("porto_settings/category/ratio_width",$code);
                    $ratio_height = Mage::getStoreConfig("porto_settings/category/ratio_height",$code);
                    $autoclose = Mage::getStoreConfig('ajaxcart/addtocart/autoclose', $code);
                    if(!($autoclose && is_numeric($autoclose)))
                        $autoclose = 5;
                    $product_image_src = "";
                    if($aspect_ratio)
                        $product_image_src=Mage::helper('catalog/image')->init($product, 'small_image')->constrainOnly(FALSE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize(250);
                    else
                        $product_image_src=Mage::helper('catalog/image')->init($product, 'small_image')->resize($ratio_width,$ratio_height);
                    $product_image = '<img src="'.$product_image_src.'" class="product-image" alt=""/>';
					$message = '<div class="msg">'.$this->__("You've just added this product to the cart:").'<p class="product-name theme-color">'.Mage::helper('core')->htmlEscape($product->getName()).'</p><div class="timer theme-color">'.$autoclose.'</div></div>'.$product_image;
					$response['status'] = 'SUCCESS';
					$response['message'] = $message;
					//New Code Here
					$this->loadLayout();
                    $toplink = "";
                    if($this->getLayout()->getBlock('minicart'))
                        $toplink = $this->getLayout()->getBlock('minicart')->toHtml();
                    $cart_sidebar = "";
                    if($this->getLayout()->getBlock('cart_sidebar'))
                        $cart_sidebar = $this->getLayout()->getBlock('cart_sidebar')->toHtml();
                    
					Mage::register('referrer_url', $this->_getRefererUrl());

                    $response['toplink'] = $toplink;
                    $response['cart_sidebar'] = $cart_sidebar;
				}
			} catch (Mage_Core_Exception $e) {
				$msg = "";
				if ($this->_getSession()->getUseNotice(true)) {
					$msg = $e->getMessage();
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$msg .= $message.'<br/>';
					}
				}

				$response['status'] = 'ERROR';
				$response['message'] = $msg;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Cannot add the item to shopping cart.');
				Mage::logException($e);
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}else{
			return parent::addAction();
		}
	}
	public function optionsAction(){
		$productId = $this->getRequest()->getParam('product_id');
		// Prepare helper and params
		$viewHelper = Mage::helper('catalog/product_view');

		$params = new Varien_Object();
		$params->setCategoryId(false);
		$params->setSpecifyOptions(false);

		// Render page
		try {
			$productHelper = Mage::helper('catalog/product');
            if (!$params) {
                $params = new Varien_Object();
            }

            // Standard algorithm to prepare and rendern product view page
            $product = $productHelper->initProduct($productId, $this, $params);
            if (!$product) {
                throw new Mage_Core_Exception($this->__('Product is not loaded'), $this->ERR_NO_PRODUCT_LOADED);
            }

            $buyRequest = $params->getBuyRequest();
            if ($buyRequest) {
                $productHelper->prepareProductOptions($product, $buyRequest);
            }

            if ($params->hasConfigureMode()) {
                $product->setConfigureMode($params->getConfigureMode());
            }

            Mage::dispatchEvent('catalog_controller_product_view', array('product' => $product));

            if ($params->getSpecifyOptions()) {
                $notice = $product->getTypeInstance(true)->getSpecifyOptionMessage();
                Mage::getSingleton('catalog/session')->addNotice($notice);
            }

            Mage::getSingleton('catalog/session')->setLastViewedProductId($product->getId());

            /***********************/
            $design = Mage::getSingleton('catalog/design');
            $settings = $design->getDesignSettings($product);

            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');
            $this->addActionLayoutHandles();

            $update->addHandle('PRODUCT_TYPE_' . $product->getTypeId());
            $update->addHandle('PRODUCT_' . $product->getId());
            $this->loadLayoutUpdates();

            // Apply custom layout update once layout is loaded
            $layoutUpdates = $settings->getLayoutUpdates();
            if ($layoutUpdates) {
                if (is_array($layoutUpdates)) {
                    foreach($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }

            $this->generateLayoutXml()->generateLayoutBlocks();

            // Apply custom layout (page) template once the blocks are generated
            /*
            if ($settings->getPageLayout()) {
                $controller->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
            }
            */

            $currentCategory = Mage::registry('current_category');
            $root = $this->getLayout()->getBlock('root');
            if ($root) {
                $controllerClass = $this->getFullActionName();
                if ($controllerClass != 'catalog-product-view') {
                    $root->addBodyClass('catalog-product-view');
                }
                $root->addBodyClass('product-' . $product->getUrlKey());
                if ($currentCategory instanceof Mage_Catalog_Model_Category) {
                    $root->addBodyClass('categorypath-' . $currentCategory->getUrlPath())
                        ->addBodyClass('category-' . $currentCategory->getUrlKey());
                }
            }
            /***********************/

            $this->initLayoutMessages(array('catalog/session', 'tag/session', 'checkout/session'))
                ->renderLayout();

		} catch (Exception $e) {
			if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
				if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
					$this->_redirect('');
				} elseif (!$this->getResponse()->isRedirect()) {
					$this->_forward('noRoute');
				}
			} else {
				Mage::logException($e);
				$this->_forward('noRoute');
			}
		}
	}
	
	 public function deleteAction(){
	    $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)->save();
                $this->_getSession()->setCartWasUpdated(true);
                if (!$this->_getCart()->getQuote()->getHasError()){
                    $response['status'] = 'SUCCESS';
                    $response['message'] = '';
                    
                    //New Code Here
                    $this->loadLayout();
                    $toplink = "";
                    if($this->getLayout()->getBlock('top.links'))
                        $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                    $cart_sidebar = "";
                    if($this->getLayout()->getBlock('cart_sidebar'))
                        $cart_sidebar = $this->getLayout()->getBlock('cart_sidebar')->toHtml();
                    Mage::register('referrer_url', $this->_getRefererUrl());
                    $response['toplink'] = $toplink;
                    $response['cart_sidebar'] = $cart_sidebar;
                }
					
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
        }        
	}
}