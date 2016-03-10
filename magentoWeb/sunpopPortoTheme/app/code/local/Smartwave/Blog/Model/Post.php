<?php

class Smartwave_Blog_Model_Post extends Mage_Core_Model_Abstract
{
    const NOROUTE_PAGE_ID = 'no-route';

    protected function _construct()
    {
        $this->_init('blog/post');
    }

    public function load($id, $field = null)
    {
        return $post = parent::load($id, $field);
    }

    public function noRoutePage()
    {
        $this->setData($this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName()));
        return $this;
    }

	public function getBannerContent() {
        $content = $this->getData('banner_content');
        if (Mage::getStoreConfig(Smartwave_Blog_Helper_Config::XML_BLOG_PARSE_CMS)) {
            $processor = Mage::getModel('core/email_template_filter');
            $content = $processor->filter($content);
        }
        return $content;
    }

    public function getShortContent()
    {
        $content = $this->getData('short_content');
        if (Mage::getStoreConfig(Smartwave_Blog_Helper_Config::XML_BLOG_PARSE_CMS)) {
            $processor = Mage::getModel('core/email_template_filter');
            $content = $processor->filter($content);
        }
        return $content;
    }

    public function getPostContent()
    {
        $content = $this->getData('post_content');
        if (Mage::getStoreConfig(Smartwave_Blog_Helper_Config::XML_BLOG_PARSE_CMS)) {
            $processor = Mage::getModel('core/email_template_filter');
            $content = $processor->filter($content);
        }
        return $content;
    }

    public function getImage() {
        $image = $this->getData('image');
        return $image;
    }
     
    public function getCategoriesForPosts($posts = array())
    {
        return $this->getResource()->getCategoriesForPost($posts);

    }

    public function loadByIdentifier($v)
    {
        return $this->load($v, 'identifier');
    }

    public function getCats()
    {
        $route = Mage::getStoreConfig('blog/blog/route');
        if ($route == "") {
            $route = "blog";
        }
        $route = Mage::getUrl($route);

        $cats = Mage::getModel('blog/cat')->getCollection()
            ->addPostFilter($this->getId())
            ->addStoreFilter(Mage::app()->getStore()->getId())
        ;

        $catUrls = array();
        foreach ($cats as $cat) {
            $catUrls[$cat->getTitle()] = $route . "cat/" . $cat->getIdentifier();
        }
        return $catUrls;
    }

    public function getDay()
    {
        return $this->getFormatted('%d');
    }

    public function getMonth()
    {
        return $this->getFormatted('%B');
    }

    public function getMonthShort()
    {
        return $this->getFormatted('%b');
    }

    private function getFormatted($pattern) {

        $orig_data = $this->getOrigData();
        $oldLocale = setlocale(LC_COLLATE, "0");

        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');
        $formatted = strftime($pattern, strtotime($orig_data['created_time']) + Mage::getSingleton('core/date')->getGmtOffset());
        setlocale(LC_ALL, $oldLocale);
    
        return $formatted;
    }
}