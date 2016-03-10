<?php

class Smartwave_Blog_Model_Blog extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('blog/blog');
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

    public function _beforeSave()
    {
        if (is_array($this->getData('tags'))) {
            $this->setData('tags', implode(",", $this->getData('tags')));
        }
        return parent::_beforeSave();
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