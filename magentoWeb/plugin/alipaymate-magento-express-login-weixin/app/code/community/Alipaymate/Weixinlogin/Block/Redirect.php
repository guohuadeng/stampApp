<?php

class Alipaymate_Weixinlogin_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $login   = Mage::getModel('weixinlogin/login');
        $config  = $login->prepareConfig();

        $weixin = Mage::getModel('weixinlogin/core');
        $weixin->setConfig($config);

        $action        = $weixin->getWeixinUrl();
        $requestHtml   = $weixin->createRequestHtml();
        $redirectText  = $this->__('You will be redirected to the Wechat website in a few seconds ...');
        $redirectTitle = $this->__('Redirect to Wechat');

        $html = <<<EOT
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>{$redirectTitle}</title>
        <style>
            .container {
                font-family: Tahoma,Verdana,Arial;
                font-size: 13px;
                margin:0 auto;
                width: 100%;
            }

            .container p {
                border: 1px solid #efefef;
                width: 600px;
                height: 65px;
                line-height: 65px;
                vertical-align: middle;
                padding: 15px;
                text-align: center;
                margin:0 auto;
                margin-top: 45px;
            }

            .container img {
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <p>{$redirectText}</p>

            <form name="weixinsubmit" id="weixinsubmit" method="post" action="{$action}">
                {$requestHtml}
            </form>
        </div>

        <script type="text/javascript">
           // document.getElementById("weixinsubmit").submit();
        </script>
    </body>
</html>
EOT;
        return $html;
    }
}
