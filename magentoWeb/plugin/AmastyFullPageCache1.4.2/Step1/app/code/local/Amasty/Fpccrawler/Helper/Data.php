<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */


/**
 * @author Amasty
 */
class Amasty_Fpccrawler_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_postData          = false;
    private $_cookieFilePath    = false;
    private $_cookieFileContent = '';

    private $_statusCodes = array(
        0 => 'Already cached',
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended'
    );

    private $_blockedUrls = array(
        'directory/currency/switch',
        'customer/account/loginPost',
        '__store=',
        'amfpccrawler',
        'customer/account/',
        '/logout',
    );

    /**
     * finds source with URL list for crawler bot to walk through
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getQueueSource()
    {
        $source         = array();
        $sourceSelected = Mage::getStoreConfig('amfpccrawler/queue/source');

        if ($sourceSelected == 'fpc') {
            /*
             * FPC built-in tables
             */
            if (Mage::helper('core')->isModuleEnabled('Amasty_Fpc')) {
                $data = Mage::getResourceModel('amfpc/url_collection')->setOrder('rate', 'DESC');
                foreach ($data as &$item) {
                    $source[] = array(
                        'rate' => $item->getRate(),
                        'url'  => $item->getUrl(),
                    );
                }
            } else {
                $this->logDebugMessage('find_resource', 'FPC selected as source, but no FPC module installed.');
                die();
            }
        } else if ($sourceSelected == 'file') {
            /*
             * load file with each new line = new url
             */
            $filePath = Mage::getStoreConfig('amfpccrawler/queue/queue_file_path');
            if (file_exists($filePath)) {
                $fileContent = file_get_contents($filePath);
                $source      = preg_split('/[,\s]+/', $fileContent, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($source as &$item) {
                    $item = array(
                        'rate' => 1,
                        'url'  => $item,
                    );
                }
            } else {
                $this->logDebugMessage('find_resource', 'File selected as source, but file do not exists with specified path: ' . $filePath);
                die();
            }
        } else if ($sourceSelected == 'sitemap') {
            /*
             * load SiteMap XML file and parse it
             */
            $filePath = Mage::getBaseDir('base') . '/sitemap.xml';
            if (file_exists($filePath)) {
                $xml = simplexml_load_file($filePath);
                foreach ($xml->url as $url) {
                    $source[] = array(
                        'rate' => round(trim((string)$url->priority) * 100), //convert float 0.5 into percent value 50%
                        'url'  => trim((string)$url->loc),
                    );
                }
            } else {
                $this->logDebugMessage('find_resource', 'Sitemap selected as source, but sitemap file do not exists in the root directory: ' . $filePath);
                die();
            }
        } else if ($sourceSelected == 'magento') {
            /*
             * fetch data from default magento URL log table
             */
            /** @var Mage_Core_Model_Resource $res */
            $res     = Mage::getSingleton('core/resource');
            $query   = "" . 'SELECT `url`, COUNT(`url_id`) as `rate` FROM ' . $res->getTableName('log_url_info') . " GROUP BY `url` ORDER BY `rate` DESC";
            $data    = $res->getConnection('core_read');
            $results = $data->fetchAll($query);
            if ($results) {
                foreach ($results as $k => &$item) {
                    $source[] = array(
                        'rate' => $item['rate'],
                        'url' => $this->getRewrittenUrl($item['url'])
                    );
                }
            }
        } else {
            $this->logDebugMessage('find_resource', 'Selected unsupported method as source for queue');
            die();
        }


        foreach ($source as $k => &$item) {
            if ($this->inIgnoreList($item['url']) || $this->containsIgnoredParams($item['url'])) {
                unset($source[$k]);
            }
        }

        return $source;
    }

    /**
     * logs debug message into special log file
     * note: it forces logging + different file for each "area"
     *
     * @param $area
     * @param $message
     */
    public function logDebugMessage($area, $message)
    {
        Mage::log($message, null, 'amfpccrawler_' . $area . '.log', true);
    }

    private function getRewrittenUrl($url)
    {
        $coreUrl = Mage::getModel('core/url_rewrite');
        $urlData = parse_url($url);
        $path    = trim($urlData['path'], '/');

        $coreUrl->load($path, 'target_path');
        $path = $coreUrl->getRequestPath();

        if (!$path) {
            $path = $url;
        } else {
            $path = Mage::getBaseUrl('web') . $path;
        }

        return $path;
    }

    /**
     * check if url is in Ignore List
     *
     * @return bool
     */
    public function inIgnoreList($path)
    {
        $ignore = Mage::getStoreConfig('amfpc/pages/ignore_list');
        $ignoreList = preg_split('|[\r\n]+|', $ignore, -1, PREG_SPLIT_NO_EMPTY);
        $ignoreList = array_unique(array_merge($this->_blockedUrls, $ignoreList));

        foreach ($ignoreList as $pattern) {
            if (preg_match("|$pattern|", $path))
                return true;
        }

        return false;
    }

    private function containsIgnoredParams($path)
    {
        if ($paramsString = Mage::getStoreConfig('amfpc/pages/ignored_params')) {
            $params = preg_split('/[,\s]+/', $paramsString, -1, PREG_SPLIT_NO_EMPTY);
            if (!empty($params)) {
                foreach ($params as $param) {
                    if (strpos($path, $param . '=') !== false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * deletes COOKIE file for specified customer group
     * e.g. removes auth data
     *
     * @param bool $customerGroup
     * @param string $currency
     */
    public function delAuthCookie($customerGroup, $currency)
    {
        $this->_cookieFilePath = Mage::getBaseDir('tmp') . "/amfpccrawler/cookies/$customerGroup.$currency.txt";
        if ($this->_cookieFilePath) {
            if (file_exists($this->_cookieFilePath)) {
                unlink($this->_cookieFilePath);
            }
            $this->_cookieFilePath    = false;
            $this->_cookieFileContent = '';
        }
    }

    /**
     * retrieves COOKIE file
     * with specified "customer_group" customer logged in
     * and
     *
     * @param string $customerGroup
     * @param string $currency
     * @param string $currencyUrl
     * @param int    $storeId
     *
     * @return bool
     */
    public function getAuthCookie($customerGroup, $currency, $currencyUrl = '', $storeId = 0)
    {
        // convert values
        $currency               = $currency ? $currency : 'default';
        $customerGroup          = $customerGroup ? $customerGroup : 'default';
        $cookieReceived         = false;

        // set cookie path
        $this->_cookieFilePath = Mage::getBaseDir('tmp') . "/amfpccrawler/cookies/$customerGroup.txt";

        // get cookie with customer group logged in
        if ($customerGroup && $customerGroup != 'default') {
            list($user, $email, $pass) = $this->getCustomerGroupCredentials($customerGroup, $storeId);

            // check if cookie already exists
            if (!file_exists($this->_cookieFilePath) || $this->cookieExpired()) {
                // empty cached cookie data and get new cookie
                $this->_cookieFileContent = '';

                // first request: just getting form_key
                list($res, $status) = $this->getUrl(Mage::getUrl('customer/account/login'));

                // get form_key
                $form_key_start        = strpos($res, 'form_key');
                $form_key_start        = strpos($res, 'value="', $form_key_start + 1); // find value of hidden input
                $form_key_end          = strpos($res, '"', $form_key_start + 8); // get end of a line (including start offset of 8 symbols
                $form_key              = substr($res, $form_key_start + 7, $form_key_end - $form_key_start - 7); // seven symbols offset is for start word
                $this->_cookieFilePath = Mage::getBaseDir('tmp') . "/amfpccrawler/cookies/$customerGroup.txt";
                $this->_postData       = "login[username]=" . ($email) . "&login[password]=" . ($pass) . "&form_key=" . ($form_key);

                // second request: authorize with given key
                list($res, $status) = $this->getUrl(Mage::getUrl('customer/account/loginPost'));

                // return success
                $this->_cookieFilePath = Mage::getBaseDir('tmp') . "/amfpccrawler/cookies/$customerGroup.txt";
                $cookieReceived = true;
            }
        }


        // get cookie with specified currency
        if ($currency && $currency != 'default') {
            $cookiePath = Mage::getBaseDir('tmp') . "/amfpccrawler/cookies/$customerGroup.$currency.txt";;
            $this->_cookieFilePath = $cookiePath;
            if (file_exists($this->_cookieFilePath) && !$this->cookieExpired()) {
                $this->_cookieFilePath = $cookiePath;
            } else {
                $this->_cookieFileContent = '';
                if (file_exists($this->_cookieFilePath)) {
                    unlink($this->_cookieFilePath);
                }
                $userCookiePath = Mage::getBaseDir('tmp') . "/amfpccrawler/cookies/$customerGroup.txt";
                if (file_exists($userCookiePath)) {
                    copy($userCookiePath, $this->_cookieFilePath);
                }
                $this->getUrl($currencyUrl);
                $this->_cookieFilePath = $cookiePath;
                $cookieReceived = true;
            }
        }

        // add special FPCcrawler flag to the cookie
        if ($cookieReceived) {
            $domainData = parse_url(Mage::getBaseUrl('web'));
            $domain     = $domainData['host'];
            $cookieFlag = '.' . $domain . '	TRUE	/	FALSE	' . strtotime('+1 month') . '	amfpc_crawler	1';
            file_put_contents($this->_cookieFilePath, $cookieFlag, FILE_APPEND | LOCK_EX);
        }

        return $this->_cookieFilePath;
    }

    /**
     * generates customer credentials for specified "customer_group"
     *
     * @param $customerGroup
     * @param $storeId
     *
     * @return array
     */
    private function getCustomerGroupCredentials($customerGroup, $storeId)
    {
        // check if user exists. if not - create user for further login
        if (!$storeId) {
            $storeId = Mage::app()
                           ->getWebsite()
                           ->getDefaultGroup()
                           ->getDefaultStoreId();
        }
        $websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
        $websiteId = $websiteId ? $websiteId : 1; // prevent from "0" value (because it is default Admin website store view)

        $user = 'FPC.Crawler.' . $customerGroup . '.' . $websiteId;
        $mail = $user . '@amasty.com';
        $hash = md5($customerGroup);
        $pass = substr($hash, 1, 5) . substr($hash, 9, 3);

        $userData = Mage::getModel("customer/customer")->setWebsiteId($websiteId)->loadByEmail($mail);
        if (!$userData->getId()) {
            $this->createUser($customerGroup, $websiteId);
        }

        return array($user, $mail, $pass);
    }

    /**
     * creating new user for specified customer group code
     *
     * @param $customerGroup
     * @param $websiteId
     *
     * @return bool
     */
    public function createUser($customerGroup, $websiteId)
    {
        $username = 'FPC.Crawler.' . $customerGroup . '.' . $websiteId;
        $email    = $username . '@amasty.com';
        $hash     = md5($customerGroup);
        $pass     = substr($hash, 1, 5) . substr($hash, 9, 3);

        $user = Mage::getModel("customer/customer")->setWebsiteId($websiteId)->loadByEmail($email);
        if (!$user->getId()) {
            try {
                // prepare new customer object
                $user = Mage::getModel('customer/customer');
                $user->setData(array(
                        'username'  => $username,
                        'firstname' => 'FPC',
                        'lastname'  => 'Crawler',
                        'email'     => $email,
                        'password'  => $pass,
                        'is_active' => 1
                    )
                );

                $user->setWebsiteId($websiteId);
                $user->setGroupId($customerGroup);

                // save customer
                $user->save();
            } catch (Exception $e) {
                $this->logDebugMessage('create_user', $e->getMessage());

                return false;
            }
        }

        return true;
    }

    /**
     * checks cookie file expiration date
     *
     * @return bool
     */
    private function cookieExpired()
    {
        if ($this->_cookieFilePath) {
            // get cookie data
            if (!$this->_cookieFileContent) {
                $this->_cookieFileContent = file_get_contents($this->_cookieFilePath);
            }
            $cookie = $this->_cookieFileContent;

            // find expired value
            $matches = array();
            $res     = preg_match_all('#[0-9]{10}#', $cookie, $matches);
            if ($res > 0) {
                $expired = min(array_values($matches[0]));
                $time    = time();
            } else {
                return true;
            }

            // if expiration date less than current (e.g. already gone and was in past) = FALSE = cookie expired
            if ($time > $expired) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * retrieve URL contents with specified parameters
     *    - group    : customer group code
     *    - store    : store ID
     *    - currency : currency code like 'USD'
     *    - mobile   : whenever mobile version must be retrieved
     *
     * @param      $url
     * @param bool $group
     * @param bool $storeId
     * @param bool $currency
     * @param bool $mobile
     * @param int  $rate
     * @param bool $logging
     *
     * @return bool
     */
    public function getUrl($url, $group = false, $storeId = false, $currency = false, $mobile = false, $rate = 0, $logging = false)
    {
        // start time point && initial data for log
        if ($logging) {
            $loadStart = time();
            $bind      = array($url, $group, $storeId, $currency, $mobile);
        }

        // check CURL lib
        if (!function_exists('curl_version')) {
            return false;
        }

        // check if any URL given
        if (!$url) {
            return false;
        } else {
            $request = curl_init();
        }

        // add store switch into GET query
        if ($storeId) {
            $store = Mage::getModel('core/store')->load($storeId)->getCode();
            $url .= (strpos($url, '?') === FALSE ? '?' : '&') . '___store=' . $store;
        }

        // get currency switch URL
        if ($currency) {
            $url = $this->getUrlWithCurrency($url, $currency);
        }

        // retrieve && attach COOKIE file for customer_group logged in user
        if ($group || $currency) {
            // get cookie
            if (!$this->getAuthCookie($group, $currency, $url, $storeId)) {
                $this->logDebugMessage('auth_cookie', 'Auth cookie retrieve fail for group: ' . $group);
                curl_close($request);

                return false;
            }
        }

        // attach cookie to the request if any cookie-path given
        if ($this->_cookieFilePath) {
            curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            curl_setopt($request, CURLOPT_COOKIESESSION, true);
            curl_setopt($request, CURLOPT_COOKIEJAR, $this->_cookieFilePath);
            curl_setopt($request, CURLOPT_COOKIEFILE, $this->_cookieFilePath);
        }

        // set default CURL params
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

        // retrieve MOBILE version
        if ($mobile) {
            curl_setopt($request, CURLOPT_USERAGENT, Mage::getStoreConfig('amfpccrawler/options/mobile_agent'));
        }

        // add POST data
        if ($this->_postData) {
            curl_setopt($request, CURLOPT_POST, true);
            curl_setopt($request, CURLOPT_POSTFIELDS, $this->_postData);
        }

        // resolve the request
        $result = curl_exec($request);
        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);

        // clean data
        curl_close($request);
        $this->_postData          = false;
        $this->_cookieFilePath    = '';
        $this->_cookieFileContent = '';

        // log results
        if ($logging) {
            /**@var Amasty_Fpccrawler_Model_Resource_Log $log */
            $log  = Mage::getResourceModel('amfpccrawler/log');
            $load = time() - $loadStart;
            list($url, $group, $storeId, $currency, $mobile) = $bind;
            $log->addToLog($url, $group, $storeId, $currency, $mobile, $rate, $status, $load);
        }

        // send result
        $acceptedStatus = Mage::getStoreConfig('amfpccrawler/options/accepted_status');
        $acceptedStatus = explode(',', $acceptedStatus);
        if (in_array($status, $acceptedStatus)) {
            return array($result, $status);
        } else {
            $this->logDebugMessage('get_url', 'Getting URL "' . $url . '" failed with status: ' . $status);

            return false;
        }

    }

    /**
     * generates special URL to switch currency
     * IMPORTANT: contains one redirect, so CURL request must follow redirects
     *
     * @param $url
     * @param $currency
     *
     * @return string
     */
    private function getUrlWithCurrency($url, $currency)
    {
        $url = Mage::getUrl('directory/currency/switch', array(
                'currency'                                                => $currency,
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core')->urlEncode($url)
            )
        );

        return $url;
    }

    public function getStatusCodeDescription($code)
    {
        if (isset($this->_statusCodes[$code])) {
            $res = $this->_statusCodes[$code];
        } else {
            $res = 'Unknown code status';
        }

        return $res;
    }

    /**
     * recursive search of a substring from array of patterns given
     *
     * @param $string string  haystack
     * @param $array  array   patterns to search
     *
     * @return bool
     */
    private function strpos_array($string, $array)
    {
        foreach ($array as $item) {
            if (strpos($string, $item) !== false) {
                return true;
            }
        }

        return false;
    }
}