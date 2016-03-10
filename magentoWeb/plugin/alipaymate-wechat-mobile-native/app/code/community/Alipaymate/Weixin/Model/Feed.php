<?php

class Alipaymate_Weixin_Model_Feed extends Mage_AdminNotification_Model_Feed
{
	public function getFeedUrl()
	{
	    $feedUrl = 'http://alipaymate.com/update/weixin/feed.rss';

	    return $feedUrl;
	}

	public function check()
	{
	    return Mage::getModel('weixin/feed')->checkUpdate();
	}

    public function getFrequency()
    {
        return 3600 * 24;
    }

    public function getLastUpdate()
    {
        return Mage::app()->loadCache('alipaymate_weixin_notifications_lastcheck');
    }

    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'alipaymate_weixin_notifications_lastcheck');

        return $this;
    }
}