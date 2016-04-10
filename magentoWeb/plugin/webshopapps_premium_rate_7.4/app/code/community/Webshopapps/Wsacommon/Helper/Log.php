<?php
/* WSA Common
 *
 * @category   Webshopapps
 * @package    Webshopapps_Wsacommon
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

/**
 * NOTE: This is deprecated. Please use Wsa Logger now instead.
 * @author Karen Baker
 *
 */
class Webshopapps_Wsacommon_Helper_Log extends Mage_Core_Helper_Abstract
{

	const SEVERITY_CRITICAL = 1;
    const SEVERITY_MAJOR    = 2;
    const SEVERITY_MINOR    = 3;
    const SEVERITY_NOTICE   = 4;
    const SEVERITY_NONE     = -1;

    /**
     *
     * Post a debug message
     *
     * @deprecated           Now part of WSALogger
     *
     * @param                $extension
     * @param string         $title
     * @param string         $description
     * @param bool           $debug
     * @param int            $code
     * @param string         $url
     * @internal param       $severity - CRITIAL,MAJOR,MINOR,NOTICE - 1-4
     */

	public static function postNotice($extension,$title,$description,$debug=true,$code=0,$url='') {

		if (!Mage::getStoreConfig('wsalogmenu/wsalog/active') || !$debug) {
    		return ;
    	}

       Mage::dispatchEvent('wsalogger_log_mesasge',
       					array('severity'=>self::SEVERITY_NOTICE,
       						  	'title' => $title,
       						  	'extension' => $extension,
       						 	'description' => $description,
       					   		'code'			=> $code,
       							'url'			=> $url
       							));
	}


    /**
     * Post a minor error message
     *
     * @deprecated   Now part of WSALogger
     *
     * @param        $extension
     * @param        $title
     * @param        $description
     * @param bool   $debug
     * @param int    $code
     * @param string $url
     */
    public static function postMinor($extension,$title,$description,$debug=true,$code=0,$url='') {

		if (!Mage::getStoreConfig('wsalogmenu/wsalog/active')) {
    		return ;
    	}


        Mage::dispatchEvent('wsalogger_log_mesasge',
       					array('severity'=>self::SEVERITY_MINOR,
       						  	'extension' => $extension,
       							'title' => $title,
       						  	'description' => $description,
       					       	'code'			=> $code,
       							'url'			=> $url
       							));
	}


    /**
     * Post a major error message
     *
     * @deprecated   Now part of WSALogger
     *
     * @param        $extension
     * @param        $title
     * @param        $description
     * @param bool   $debug
     * @param int    $code
     * @param string $url
     */
    public static function postMajor($extension,$title,$description,$debug=true,$code=0,$url='') {

		if (!Mage::getStoreConfig('wsalogmenu/wsalog/active')) {
    		return ;
    	}

        Mage::dispatchEvent('wsalogger_log_mesasge',
       					array('severity'=>self::SEVERITY_MAJOR,
       						  	'title' => $title,
       						  	'extension' => $extension,
       							'description' => $description,
       					   		'code'			=> $code,
       							'url'			=> $url
       							));
	}

    /**
     * Post a critical error
     *
     * @deprecated   Now part of WSALogger
     *
     * @param        $extension
     * @param        $title
     * @param        $description
     * @param bool   $debug
     * @param int    $code
     * @param string $url
     */
    public static function postCritical($extension,$title,$description,$debug=true,$code=0,$url='') {

		if (!Mage::getStoreConfig('wsalogmenu/wsalog/active')) {
    		return ;
    	}

       	Mage::dispatchEvent('wsalogger_log_mesasge',
       					array('severity'=>self::SEVERITY_CRITICAL,
       						  	'title' => $title,
       						  	'extension' => $extension,
       							'description' => $description,
       							'code'			=> $code,
       							'url'			=> $url
       							));
	}


    /**
     * Get possible error states
     *
     * @deprecated   Now part of WSALogger
     *
     * @param null   $severity
     * @return array|null
     */
    public function getSeverities($severity = null)
    {
        $severities = array(
            self::SEVERITY_CRITICAL => Mage::helper('adminnotification')->__('Critical'),
            self::SEVERITY_MAJOR    => Mage::helper('adminnotification')->__('Major'),
            self::SEVERITY_MINOR    => Mage::helper('adminnotification')->__('Minor'),
            self::SEVERITY_NOTICE   => Mage::helper('adminnotification')->__('Notice'),
            self::SEVERITY_NONE     => Mage::helper('adminnotification')->__('None'),
        );

        if (!is_null($severity)) {
            if (isset($severities[$severity])) {
                return $severities[$severity];
            }
            return null;
        }

        return $severities;
    }


}