<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */

class Amasty_Fpc_Model_Fpc extends Mage_Core_Model_Cache
{
    const CACHE_TAG = 'AMFPC';

    const TYPE_INDEX = 'amfpc_index';

    const COMPRESSION_PREFIX = 'CMP';

    const STATS_MODE = false;

    protected $_backendType;
    protected $_storageSize = null;
    protected $_maxStorageSize = null;

    protected $_blockReserve = .2; // Percents of storage reserved for blocks

    protected $_discardedBlocks = array();

    public function __construct(array $options = array())
    {
        $configOptions = Mage::app()->getConfig()->getNode('global/amfpc/options');

        if ($configOptions) {
            $configOptions = $configOptions->asArray();
        } else {
            $configOptions = array();
        }

        $options = array_merge($configOptions, $options);

        if (isset($options['backend_options']['cache_dir']))
        {
            $options['backend_options']['cache_dir'] = Mage::getBaseDir('var') . DS . $options['backend_options']['cache_dir'];
            $exists = Mage::app()->getConfig()->getOptions()->createDirIfNotExists($options['backend_options']['cache_dir']);

            if (!$exists){ // Use default /var/cache directory if it's not possible to create /var/amasty_fpc
                unset($options['backend_options']['cache_dir']);
            }
        }

        $this->_backendType = $options['backend'];

        $this->_initDiscardedBlocks();

        parent::__construct($options);
    }

    protected function _initDiscardedBlocks()
    {
        if (Mage::helper('core')->isModuleEnabled('Amasty_Shopby')) {

            $agentString = Mage::getStoreConfig('amshopby/seo/exclude_user_agent');

            if ($agentString) {
                $matched = false;

                /** @var Mage_Core_Helper_Http $helper */
                $helper = Mage::helper('core/http');
                $currentAgent = $helper->getHttpUserAgent();

                $excludeAgents = explode(',', $agentString);
                foreach ($excludeAgents as $agent) {
                    if (stripos($currentAgent, trim($agent)) !== false) {
                        $matched = true;
                        break;
                    }
                }

                $agents = implode('|', $excludeAgents);

                $this->_discardedBlocks['Amasty_Shopby_Block_Catalog_Layer_View'] = array(
                    'agents' => $agents,
                    'matched' => $matched
                );
            }
        }
    }

    public function getDiscardedBlocks()
    {
        return $this->_discardedBlocks;
    }

    public function setReadonly($readonly = true)
    {
        $this->_disallowSave = $readonly;
    }

    protected function _getBackendOptions(array $cacheOptions)
    {
        $options = parent::_getBackendOptions($cacheOptions);

        if ($this->_backendType == 'Amasty_Fpc_Backend_Database')
        {
            $options['options'] = $this->getDbAdapterOptions($options);
        }

        return $options;
    }

    public function getMaxStorageSize()
    {
        if ($this->_maxStorageSize === null) {
            $this->_maxStorageSize = +Mage::getStoreConfig(
                'amfpc/compression/max_size'
            );
        }

        return $this->_maxStorageSize;
    }

    public function getStorageSize()
    {
        if ($this->_storageSize === null) {
            $adapter = $this->_getResource()->getReadConnection();
            $config = $adapter->getConfig();
            $select = new Varien_Db_Select($adapter);

            $select
                ->from('information_schema.TABLES', '')
                ->columns('(data_length + index_length)')
                ->where('table_schema = ?', $config['dbname'])
                ->where(
                    'table_name = ?',
                    $this->_getResource()->getTable('core/cache')
                );

            $this->_storageSize = $adapter->fetchOne($select);
        }

        return $this->_storageSize / 1024 / 1024;
    }

    public function getBackendType()
    {
        return $this->_backendType;
    }

    protected function _minify($content)
    {
        $search = array(
            '/[\r\n]+/s',
            '/[ \t]+/s',
            '/[ \t]*\n[ \t]*/s',
            '/\n+/s',
        );

        $replace = array(
            "\n",
            " ",
            "\n",
            "\n"
        );

        return preg_replace($search, $replace, $content);
    }

    public function saveCache($data, $tags, $lifetime)
    {
        if ((FALSE !== stripos($this->_backendType, 'database'))
            && $this->getMaxStorageSize() > 0)
        {
            if ($this->getStorageSize() >= $this->getMaxStorageSize() * (1-$this->_blockReserve)){
                return;
            }
        }

        Mage::helper('amfpc')->cutHoles($data);

        $key = Mage::getSingleton('amfpc/fpc_front')->getCacheKey();

        if ($formKey = Mage::getSingleton('core/session')->getFormKey())
        {
            $data = str_replace($formKey, 'AMFPC_FORM_KEY', $data);
        }

        $data = $this->_minify($data);

        $data = array(
            'content' => $data,
        );

        if ($product = Mage::registry('current_product'))
        {
            $data['product_id'] = $product->getId();
        }

        if ($category = Mage::registry('current_category'))
        {
            $data['category_id'] = $category->getId();
        }

        if ($store = Mage::app()->getStore())
        {
            $data['store_id'] = $store->getId();
        }

        $data = serialize($data);

        if (Mage::helper('amfpc')->isPageCompressionEnabled())
        {
            $data = gzcompress($data, +Mage::getStoreConfig('amfpc/compression/level'));
            $data = base64_encode($data);
            $data = self::COMPRESSION_PREFIX . $data;
        }

        if (self::STATS_MODE)
        {
            Mage::getModel('amfpc/stats')
                ->load($key, 'cache_id')
                ->addData(array(
                    'cache_id' => $key,
                    'url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                    'size' => strlen($data),
                    'type' => 'page',
                    'customer_group' => Mage::getSingleton('amfpc/fpc_front')->getCustomerGroupId(),
                    'session' => $_COOKIE['frontend']
                ))
                ->save();
        }

        $this->save($data, $key, $tags, $lifetime);
    }

    public function validateBlocks(Mage_Core_Controller_Request_Http $request)
    {
        $config = Mage::getSingleton('amfpc/config')->getConfig();

        foreach ($config['blocks'] as $name => $block)
        {
            if (isset($block['route']))
                $routes = array($block['route']);
            else if (isset($block['routes']))
                $routes = $block['routes'];
            else
                continue;

            foreach ($routes as $route)
            {
                if ($this->matchRoute($request, $route))
                {
                    Mage::getSingleton('amfpc/fpc')->removeBlockCache($name);
                    Mage::getSingleton('amfpc/session')->updateBlock($name);
                    break;
                }
            }
        }
    }

    public function matchRoute(Mage_Core_Controller_Request_Http $request, $route)
    {
        $path = explode('/', $route);

        for ($i = 0; $i < 3; $i++)
        {
            if (!isset($path[$i]))
                $path[$i] = 'index';
            else if ($path[$i][0] == '(') // multiple values
            {
                $path[$i] = explode('|', trim($path[$i], '()'));
            }
        }

        return $this->_compareComponents($path, array(
            $request->getRouteName(),
            $request->getControllerName(),
            $request->getActionName()
        ));

    }

    protected function _compareComponents($patterns, $values)
    {
        foreach ($patterns as $i => $pattern)
        {
            if ($pattern == '*')
                continue;
            else if (is_array($pattern)) // OR
            {
                $match = false;
                foreach ($pattern as $subpattern)
                {
                    if ($subpattern == $values[$i])
                        $match = true;
                }

                if (!$match)
                    return false;
            }
            else if ($pattern != $values[$i])
                return false;
        }

        return true;
    }

    public function removeBlockCache($name)
    {
        $blockTag = Mage::getSingleton('amfpc/fpc_front')->getBlockCacheTag($name);
        $this->clean($blockTag);
    }

    public function load($id)
    {
        $content = parent::load($id);

        if ($content)
        {
            $prefixLen = strlen(self::COMPRESSION_PREFIX);
            if (substr($content, 0, $prefixLen) == self::COMPRESSION_PREFIX)
            {
                $content = gzuncompress(base64_decode(substr($content, $prefixLen)));
            }
        }

        return $content;
    }

    public function saveBlockCache($name, $content, $tags)
    {
        if (in_array($name, array('global_messages', 'messages')))
            return;

        if ($name == Amasty_Fpc_Model_Config::getCookieNoticeBlockName()) {
            if (!trim($content))
                return;
        }

        if ((FALSE !== stripos($this->_backendType, 'database'))
            && $this->getMaxStorageSize() > 0)
        {
            if ($this->getStorageSize() >= $this->getMaxStorageSize()){
                return;
            }
        }

        if (!$tags)
            $tags = array();

        $id = Mage::getSingleton('amfpc/fpc_front')->getBlockCacheId($name);

        $blockTag = Mage::getSingleton('amfpc/fpc_front')->getBlockCacheTag($name);

        $tags[] = Mage_Core_Block_Abstract::CACHE_GROUP;
        $tags[] = $blockTag;

        $lifetime = +Mage::getStoreConfig('amfpc/general/block_lifetime');

        $lifetime *= 3600;

        $content = $this->removeSid($content);

        if ($formKey = Mage::getSingleton('core/session')->getFormKey())
        {
            $content = str_replace($formKey, 'AMFPC_FORM_KEY', $content);
        }

        if (Mage::helper('amfpc')->isBlockCompressionEnabled())
        {
            $content = gzcompress($content, +Mage::getStoreConfig('amfpc/compression/level'));
            $content = base64_encode($content);
            $content = self::COMPRESSION_PREFIX . $content;
        }

        if (self::STATS_MODE)
        {
            Mage::getModel('amfpc/stats')
                ->load($id, 'cache_id')
                ->addData(array(
                    'cache_id' => $id,
                    'url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                    'size' => strlen($content),
                    'type' => 'block',
                    'block_name' => $name,
                    'customer_group' => Mage::getSingleton('amfpc/fpc_front')->getCustomerGroupId(),
                    'session' => isset($_COOKIE['frontend']) ? $_COOKIE['frontend'] : ''
                ))
                ->save();
        }

        $this->save($content, $id, $tags, $lifetime);
    }

    public function removeSid($content)
    {
        $content = preg_replace_callback('#(\?|&amp;|&)___SID=([SU])(&amp;|&)?#',
            array($this, "sessionVarCallback"), $content);

        return $content;
    }

    public function sessionVarCallback($match)
    {
        if ($match[1] == '?' && isset($match[3])) {
            return '?';
        } elseif ($match[1] == '?' && !isset($match[3])) {
            return '';
        } elseif (($match[1] == '&amp;' || $match[1] == '&') && !isset($match[3])) {
            return '';
        } elseif (($match[1] == '&amp;' || $match[1] == '&') && isset($match[3])) {
            return $match[3];
        }
        return '';
    }

    public function getProductsAdditionalTags($productIds)
    {
        $additionalTags = array();

        if (!empty($productIds))
        {
            /**
             * @var Mage_Core_Model_Resource $resource
             * @var Varien_Db_Adapter_Pdo_Mysql $connection
             */

            $resource = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_read');

            /**
             * @var Varien_Db_Select $select
             */
            $select = $connection->select()
                ->from($resource->getTableName('catalog/category_product_index'), 'category_id')
                ->where('product_id IN (?)', $productIds)
                ->distinct()
            ;

            $categoryIds = $connection->fetchCol($select);

            if ($categoryIds)
            {
                foreach ($categoryIds as $categoryId)
                {
                    $additionalTags[] = 'catalog_category_' . $categoryId;
                }
            }

            $additionalProducts = Mage::getResourceSingleton('catalog/product_type_configurable')
                ->getParentIdsByChild($productIds);

            $linkCollection = Mage::getResourceModel('catalog/product_link_collection')
                ->addFieldToFilter('linked_product_id', array('in' => $productIds))
            ;

            $linkSelect = $linkCollection
                ->getSelect()
                ->reset(Varien_Db_Select::COLUMNS)
                ->columns('product_id')
                ->distinct()
            ;

            $linkedProductIds = $linkCollection->getConnection()->fetchCol($linkSelect);

            if ($linkedProductIds)
            {
                $additionalProducts = array_merge($additionalProducts, $linkedProductIds);
            }

            foreach ($additionalProducts as $productId)
            {
                $additionalTags[] = 'catalog_product_' . $productId;
            }
        }

        return $additionalTags;
    }

    public function getCacheKey($params)
    {
        $url = explode('?', $params['url']);

        $mobile = $params['mobile'] ? 'm' : false;

        if (Mage::getStoreConfigFlag('amfpc/general/no_groups')){
            $customerGroup = false;
        }
        else {
            $customerGroup = +$params['customerGroup'];
        }

        if (is_numeric($params['store'])) {
            $defaultStore = Mage::app()->getDefaultStoreView();
            $store = Mage::app()->getStore($params['store']);

            if ($defaultStore->getId() == $store->getId()){
                $store = false;
            }
            else {
                $store = $store->getCode();
            }
        }
        else {
            $store = false;
        }

        $key = 'amfpc_' . $mobile . $params['currency'] . $store . $customerGroup . $url[0];

        if (sizeof($url) > 1) {
            parse_str($url[1], $getParams);
            $getParams = Mage::getSingleton('amfpc/fpc_front')->removeDisregardedParams($getParams);
            $queryString = '?' . http_build_query($getParams);

            $key .= $queryString;
        }

        $key = sha1($key);

        return $key;
    }
}
