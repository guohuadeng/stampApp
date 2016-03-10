<?php

class Smartwave_Blog_Model_Url extends Mage_Core_Model_Url
{

    public function getUrl($routePath = null, $routeParams = null)
    {
        $route = Mage::helper('blog')->getRoute();
        if (!empty($route)) {
            $isUseCategoryUrl = Mage::helper('blog')->isCategoryUrl();
            $category = Mage::getSingleton('blog/cat');
            $post = Mage::getSingleton('blog/post');
            $tag = $this->getRequest()->getParam('tag', false);
            if ($isUseCategoryUrl && $category->getCatId()) {
                $route .= '/' . Smartwave_Blog_Helper_Data::CATEGORY_URI_PARAM . '/' . $category->getIdentifier();
            }
            if ($post->getIdentifier()) {
                if ($isUseCategoryUrl && $category->getCatId()) {
                    $route .= '/' . Smartwave_Blog_Helper_Data::POST_URI_PARAM . '/' . $post->getIdentifier();
                }
                else {
                    $route .= '/' . $post->getIdentifier();
                }
            }
            if ($tag) {
                $route .= '/' . Smartwave_Blog_Helper_Data::TAG_URI_PARAM . '/' . $tag;
            }
            $routePath = $route;
        }
        return parent::getUrl($routePath, $routeParams);
    }

}
