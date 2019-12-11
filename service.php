<?php

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//载入核心文件
require_once __DIR__ . '/core.php';

\DB\AdminCenter\RegisterTables::register();

die();

// 设置微信支付配置
use Lib\WxPay\PayApi\WxPayApi;
use Lib\WxPay\PayConfig\WxPayConfig;
use Lib\WxPay\PayData\WxPayUnifiedOrder;
use Lib\WxPay\PayException\WxPayException;

WxPayConfig::getInstance()
    ->setMerchantId('1509036011')
    ->setAppId('wx568c4ec67bb0f32e')
    ->setSignType('MD5')
    ->setPayKey('')
    ->setAppSecret('cdcc40fc170b0ec9b7c390b0a0c07da6')
    ->setNotifyUrl('https://testpay.jiaheyx.com/paycenter/test.php');

//②、统一下单
$notify_url = '';
$inputObj = WxPayUnifiedOrder::getInstance()
    ->setBody('牵手常德棋牌-测试-H5支付')
    ->setAttach('H5')
    ->setOutTradeNo(date('YmdHis') . mt_rand(10000, 99999))
    ->setTotalFee(1)
    ->setTimeStart(date("YmdHis"))
    ->setTimeExpire(date("YmdHis", time() + 600))
    ->setTradeType('MWEB');
try {
    $order = WxPayApi::unifiedOrder($inputObj);
    $query_string = 'https://testpay.jiaheyx.com/majiangcd/api/mobile/payreferer.php?topay=1&url=' . urlencode($order['mweb_url']);
    echo '<a href="' . $query_string . '">微信支付</a>';
} catch (WxPayException $ex) {
    die($ex->errorMessage());
}

