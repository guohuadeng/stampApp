<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */

class Amasty_Fpc_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function inIgnoreList()
    {
        $ignore = Mage::getStoreConfig('amfpc/pages/ignore_list');
        $ignoreList = preg_split('|[\r\n]+|', $ignore, -1, PREG_SPLIT_NO_EMPTY);

        $path = Mage::app()->getRequest()->getOriginalPathInfo();
        $uri = Mage::app()->getRequest()->getRequestUri();

        foreach ($ignoreList as $pattern)
        {
            if (preg_match("|$pattern|", $path) || preg_match("|$pattern|", $uri))
                return true;
        }

        return false;
    }

    public function isPageCompressionEnabled()
    {
        $entities = Mage::getStoreConfig('amfpc/compression/entities');

        return in_array($entities, array(
            Amasty_Fpc_Model_Config_Source_Compression::COMPRESSION_ALL,
            Amasty_Fpc_Model_Config_Source_Compression::COMPRESSION_PAGES
        ));
    }

    public function isBlockCompressionEnabled()
    {
        $entities = Mage::getStoreConfig('amfpc/compression/entities');

        return $entities == Amasty_Fpc_Model_Config_Source_Compression::COMPRESSION_ALL;
    }

    public function invalidateBlocksWithAttribute($attributes)
    {
        $config = Mage::getSingleton('amfpc/config')->getConfig();

        if (!is_array($attributes))
            $attributes = array($attributes);

        foreach ($config['blocks'] as $name => $block)
        {
            foreach ($attributes as $attribute)
            {
                if (isset($block['@']) && isset($block['@'][$attribute]))
                {
                    Mage::getSingleton('amfpc/session')->updateBlock($name);
                    Mage::getSingleton('amfpc/fpc')->removeBlockCache($name);

                    break;
                }
            }
        }
    }

    public function cutHoles(&$html)
    {
        $stack = array();

        if (preg_match_all('#(<(?P<tag>amfpc(?:_ajax)?)[\s\n]+(?P<attributes>[^>]*?)>|</amfpc(?:_ajax)?>)#s',
            $html,
            $matches,
            PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER))
        {
            for ($i = sizeof($matches[1]) - 1; $i >= 0; $i--)
            {
                if (!is_array($matches['tag'][$i]) || $matches['tag'][$i][1] < 0) // Closing tag
                {
                    array_push($stack, $matches[1][$i][1] + strlen($matches[1][$i][0]));
                }
                else // Opening tag
                {
                    $startPosition = $matches[1][$i][1];
                    $endPosition = array_pop($stack);

                    if (sizeof($stack) == 0) // Top level of nesting
                    {
                        $attributes = $matches['attributes'][$i][0];
                        $tag = $matches['tag'][$i][0];
                        $html = substr_replace($html, "<{$tag} {$attributes} />", $startPosition, $endPosition - $startPosition);
                    }
                }
            }
        }
    }

    public function replaceFormKey($html)
    {
        $key = Mage::getSingleton('core/session')->getFormKey();
        $html = str_replace('AMFPC_FORM_KEY', $key, $html);

        return $html;
    }
}
