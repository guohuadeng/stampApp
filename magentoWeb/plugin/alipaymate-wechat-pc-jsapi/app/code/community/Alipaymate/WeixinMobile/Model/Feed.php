<?php

class Alipaymate_WeixinMobile_Model_Feed extends Mage_AdminNotification_Model_Feed
{
	public function getFeedUrl()
	{
	    $feedUrl = 'http://alipaymate.com/update/weixinmobile/feed.rss';

	    return $feedUrl;
	}

	public function check()
	{
	    return Mage::getModel('weixinmobile/feed')->checkUpdate();
	}

    public function getFrequency()
    {
        return 3600 * 24;
    }

    public function getLastUpdate()
    {
        return Mage::app()->loadCache('alipaymate_weixinmobile_notifications_lastcheck');
    }

    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'alipaymate_weixinmobile_notifications_lastcheck');

        return $this;
    }
}