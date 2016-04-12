<?php

class Sunpop_WeixinApp_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $payment = Mage::getModel('weixinapp/payment');
        $config  = $payment->prepareConfig();
        $params  = $payment->prepareBizData();

        $weixin = Mage::getModel('weixinapp/core');
        $weixin->setConfig($config);
        $weixin->setBizParams($params);
                
        $weixin->getOpenid();

        $jsapi_parameters = $weixin->getJsApiParameters();

        $redirectText  = $this->__('Proccessing, Please wait a moment...');
        $redirectTitle = $this->__('Weixin App Payment');

        $returnUrl = $payment->getReturnURL();

        $html = <<<EOT
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>{$redirectTitle}</title>
        <style>
            .container {
                font-size: 14px;
                margin:0 auto;
                width: 100%;
            }

            .container p {
                vertical-align: middle;
                padding: 15px;
                text-align: center;
                margin:0 auto;
            }

            .container img {
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <p>{$redirectText}</p>
        </div>

        <script type="text/javascript">
            callpay();

	        function jsApiCall()
	        {
	        	WeixinJSBridge.invoke(
	        		'getBrandWCPayRequest',
	        		{$jsapi_parameters},
	        		function(res){
                        if (res.err_msg == 'get_brand_wcpay_request:ok') {
                             window.location.href = "{$returnUrl}";
                        }
	        		}
	        	);
	        }

	        function callpay()
	        {
	        	if (typeof WeixinJSBridge == "undefined"){
	        	    if( document.addEventListener ){
	        	        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
	        	    }else if (document.attachEvent){
	        	        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
	        	        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
	        	    }
	        	}else{
	        	    jsApiCall();
	        	}
	        }
        </script>
    </body>
</html>
EOT;
        return $html;
    }
}
