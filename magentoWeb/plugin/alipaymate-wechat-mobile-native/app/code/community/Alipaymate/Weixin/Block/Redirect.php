<?php

require_once MAGENTO_ROOT . DS . 'lib/phpqrcode/phpqrcode.php';

class Alipaymate_Weixin_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $request = $this->getRequest()->getParams();

        if (isset($request['orderId']) && $request['orderId'] > '') {
            $orderId = $request['orderId'];
        } else {
            $session = Mage::getSingleton('checkout/session');
            $orderId = $session->getLastRealOrderId();
        }
        $payment = Mage::getModel('weixin/payment');
        $config  = $payment->prepareConfig();
        $params  = $payment->prepareBizData();
        $total = sprintf("%.2f", $params['total_fee'] / 100.0);

        $weixin = Mage::getModel('weixin/core');
        $weixin->setConfig($config);
        $weixin->setBizParams($params);
        $url = $weixin->makePayUrl();

        $tmpfile = sys_get_temp_dir() . DS . uniqid();
        QRcode::png($url, $tmpfile, 'H', 5);
        $imgdata = 'data:image/png;base64,' . base64_encode(file_get_contents($tmpfile));
        unlink($tmpfile);
        
        $returnUrl = $payment->getReturnURL($orderId);
        $paidUrl   = $payment->getPaidURL($orderId);

        $html = <<<EOT
<!doctype html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<title>微信支付</title>
<link href="//qzonestyle.gtimg.cn/open_proj/proj_qcloud_v2/css/shop_cart/wechat_pay.css?v=20150508" rel="stylesheet" media="screen"/>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.7.2/jquery.min.js"></script>
</head>
<body>
<div class="body">
    <h1 class="mod-title">
        <span class="ico-wechat"></span><span class="text">微信支付</span>
    </h1>
    <div class="mod-ct">
        <div class="order">
        </div>
        <div class="amount">￥{$total}</div>
        <div class="qr-image" style="display:true">
            <img id="billImage" style="width:300px;height:300px;" src="{$imgdata}" />
        </div>

        <div class="tip">
            <span class="dec dec-left"></span>
            <span class="dec dec-right"></span>
            <div class="ico-scan"></div>
            <div class="tip-text">
                <p>请使用微信扫一扫</p>
                <p>扫描二维码完成支付</p>
                <p>微信内请长按二维码并识别</p>
            </div>
        </div>
     </div>
</div>

<script>
jQuery(document).ready(function($) {    
    (function poll() {
       setTimeout(function() {
           $.ajax(
                { url: "{$paidUrl}", 
                  success: function(data) {                    
                    if ($.trim(data) == 'ok') {
                      setTimeout(function() { //因为支付后订单仍要变更状态，故增加一点处理时间
                        $(window.location).attr('href', '{$returnUrl}');
                        },1000);
                    }
                }, 
                complete: poll 
           });
        }, 3000);
    })();
});
</script>

</body>
</html>
EOT;

        echo $html;
    }

}
