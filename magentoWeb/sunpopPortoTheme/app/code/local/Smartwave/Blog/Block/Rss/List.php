<?php

class Smartwave_Blog_Block_Rss_List extends Mage_Rss_Block_List
{
    public function getRssMiscFeeds()
    {
        parent::getRssMiscFeeds();
        $this->SmartwaveBlogFeed();
        return $this->getRssFeeds();
    }

    public function SmartwaveBlogFeed()
    {
        $route = Mage::helper('blog')->getRoute() . '/rss';
        $title = Mage::getStoreConfig('blog/blog/title');
        $this->addRssFeed($route, $title);
        return $this;
    }
}