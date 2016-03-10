<?php
class Smartwave_Ajaxcart_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * Path for config.
     */
    const XML_CONFIG_PATH = 'ajaxcart/general/';

    /**
     * Name library directory.
     */
    const NAME_DIR_JS = 'ajaxcart/jquery/';

    /**
     * List files for include.
     *
     * @var array
     */
    protected $_files = array(
        'jquery-1.8.1.min.js',
        'jquery.noconflict.js',
    );

    /**
     * Check enabled.
     *
     * @return bool
     */
    public function isJqueryEnabled()
    {
        return (bool) $this->_getConfigValue('jquery', $store = '');
    }

    /**
     * Return path file.
     *
     * @param $file
     *
     * @return string
     */
    public function getJQueryPath($file)
    {
        return self::NAME_DIR_JS . $file;
    }

    /**
     * Return list files.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->_files;
    }

	public function isBannerNextModuleEnabled()
    {
        return (bool) $this->_getConfigValue('active', $store = '');
    }
	
	public function isResponsiveBannerEnabled()
    {
        return (bool) $this->_getConfigValue('responsive_banner', $store = '');
    }
	
    protected function _getConfigValue($key, $store)
    {
        return Mage::getStoreConfig(self::XML_CONFIG_PATH . $key, $store = '');
    }
	
}