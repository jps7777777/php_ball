<?php
ini_set('date.timezone', 'Asia/Shanghai');
//error_reporting(E_ERROR);
require_once dirname(__DIR__) . '/lib/WxPay.Api.php';
// "../lib/WxPay.Api.php";
//var_dump(require_once "../lib/WxPay.Api.php");
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志
$logHandler = new CLogFileHandler("../logs/" . date('Y-m-d') . '.log');
$log = Log::Init($logHandler, 15);

//打印输出数组信息
function printf_info($data) {
    foreach ($data as $key => $value) {
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

$order_desc = "全域国际-" . $orderinfo['desc'];
$order_fee = $orderinfo['order_money'] * 100;
$order_no = $orderinfo['order_sn'];

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($order_desc); //描述
$input->SetAttach($order_desc); //附加参数
$input->SetOut_trade_no($order_no); //订单编号
$input->SetTotal_fee($order_fee); //金额
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url("http://www.pawzqy.com/Mobile/orders/notify");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);

//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
//printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/> 
        <title>微信支付-全域国际</title>
        <style>
            *{
                margin:0;
                padding:0;
            }
            a{
                text-decoration: none;
            }
            body{
                background:#f6f6f6;
            }
            em{
                font-style: normal;
            }
            .header_y {
                height: 60px;
                background: #333;
                color: #fff;
                text-align: center;
                line-height: 60px;
                position: relative;
                padding: 0 10px; }
            /* line 86, ../sass/main.scss */
            .header_y .index_left_y {
                color: #fff;
                font-size: 18px;
                padding-left: 10px;
                position: absolute;
                top: 0px;
                left: 20px; }
            /* line 91, ../sass/main.scss */
            .header_y .index_left_y:before {
                content: "";
                display: inline-block;
                width: 10px;
                height: 10px;
                border-bottom: 1px solid #fff;
                border-right: 1px solid #fff;
                -webkit-transform: rotate(135deg);
                -ms-transform: rotate(135deg);
                transform: rotate(135deg); }
            /* line 95, ../sass/main.scss */
            .header_y .index_right_y {
                color: #fff;
                font-size: 18px;
                padding-left: 10px;
                position: absolute;
                top: 0px;
                right: 20px; }
            /* line 100, ../sass/main.scss */
            .header_y .index_right_y img {
                margin-top: 23px; }
            /* line 104, ../sass/main.scss */
            .header_y h1 {
                font-size: 24px; font-weight: normal;}

            .meituan_y h3{
                text-align:center;
                font-size:16px;
                line-height:50px;
            }.meituan_y h2{
                font-size:30px;
                line-height:50px;
                text-align:center;
            }
            .meituan_y .resive_y{
                background:#fff;
                border-top:1px solid #ddd;
                border-bottom:1px solid #ddd;
                color:#999;
                font-size:18px;
                padding:0 10px;
                line-height:50px;
                margin-top:30px;
            }
            .resive_y .fr{
                float:right;
                color:#333;
                font-size:18px;
            }
            .btn_wrap_y{
                padding:0 10px;
                margin-top:30px;
            }
            .btn_wrap_y button{
                background:#19ad19;
                color:#fff;
                line-height:50px;
                width:100%;
                height:50px;
                font-size:18px;
                border:none;
                border-radius:10px;
            }




        </style>

        <script type="text/javascript">
            //调用微信JS api 支付
            function jsApiCall()
            {
                WeixinJSBridge.invoke(
                        'getBrandWCPayRequest',
<?php echo $jsApiParameters; ?>,
                        function (res) {
                            if (res.err_msg == 'get_brand_wcpay_request:ok') {

                                window.location.href = 'http://www.pawzqy.com/mobile/order/pay_success';
                            } else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                                window.location.href = "http://www.pawzqy.com/mobile/users/myorder/order_status/1";
                            }
                        }
                );
            }

            function callpay()
            {
                if (typeof WeixinJSBridge == "undefined") {
                    if (document.addEventListener) {
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                    } else if (document.attachEvent) {
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                    }
                } else {
                    jsApiCall();
                }
            }
        </script>
        <script type="text/javascript">
            //获取共享地址
            function editAddress()
            {
                WeixinJSBridge.invoke(
                        'editAddress',
<?php echo $editAddress; ?>,
                        function (res) {
                            var value1 = res.proviceFirstStageName;
                            var value2 = res.addressCitySecondStageName;
                            var value3 = res.addressCountiesThirdStageName;
                            var value4 = res.addressDetailInfo;
                            var tel = res.telNumber;

//				alert(value1 + value2 + value3 + value4 + ":" + tel);
                        }
                );
            }

            window.onload = function () {
                if (typeof WeixinJSBridge == "undefined") {
                    if (document.addEventListener) {
                        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
                    } else if (document.attachEvent) {
                        document.attachEvent('WeixinJSBridgeReady', editAddress);
                        document.attachEvent('onWeixinJSBridgeReady', editAddress);
                    }
                } else {
                    editAddress();
                }
            };

        </script>
    </head>
    <link rel="stylesheet" href="./Public/static/bootstrap/css/bootstrap.css">

    <body>
        <section class="meituan_y">
            <h3>订单- <em><?php echo $order_desc ?></em></h3>
            <h2><em>￥</em><em><?php echo $orderinfo['order_money']; ?></em></h2>
            <p class="resive_y">收款方<span class="fr">全域国际</span></p>
            <p class="btn_wrap_y"><button  onclick="callpay()" >立即支付</button></p>

        </section>
    </body>
</html>