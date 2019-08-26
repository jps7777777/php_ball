<?php
ini_set('date.timezone', 'Asia/Shanghai');
//error_reporting(E_ERROR);
require_once dirname(__DIR__) . '/lib/WxPay.Api.php';
// "../lib/WxPay.Api.php";
//var_dump(require_once "../lib/WxPay.Api.php");
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志
//$logHandler = new CLogFileHandler("../logs/" . date('Y-m-d') . '.log');
//$log = Log::Init($logHandler, 15);

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
echo $openId;die();
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
echo $order;die();
//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
//printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

?>