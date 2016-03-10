<?php

class Smartwave_QuickView_Model_Observer
{
    public function compareRedirect($observer)
    {
        $referrer = Mage::app()->getRequest()->getServer('HTTP_REFERER');
        if ($referrer) {
            Mage::app()->getResponse()->setRedirect($referrer);
        }
        return $this;
    }

    public function checkRefererUrl($observer)
    {
        $lastValidReferrer = Mage::getSingleton('core/session')->getQuickViewLastValidReferrer();
        $referrerUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : Mage::getBaseUrl();
        // ---
        if (strpos($referrerUrl, '/quickview/')) {
            $_SERVER['HTTP_REFERER'] = $lastValidReferrer;
        } else {
            $lastValidReferrer = $referrerUrl;
        }
        Mage::getSingleton('core/session')->setQuickViewLastValidReferrer($lastValidReferrer);
        Mage::getSingleton('checkout/session')->setContinueShoppingUrl($lastValidReferrer);
        return $this;
    }
}
