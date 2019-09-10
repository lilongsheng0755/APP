<?php
/**
 * Author: lilongsheng
 * CreateTime: 2019/9/10 13:45
 * Description:
 */
//载入核心文件
require_once __DIR__ . '/core.php';

file_put_contents('wxpay_callback.log', file_get_contents('php://input') . PHP_EOL. PHP_EOL, FILE_APPEND);
use Lib\WxPay\PayConfig\WxPayConfig;
use Lib\WxPay\PayResults\WxPayNotify;


WxPayConfig::getInstance()
    ->setMerchantId('1509036011')
    ->setAppId('wx568c4ec67bb0f32e')
    ->setSignType('MD5')
    ->setPayKey('')
    ->setAppSecret('cdcc40fc170b0ec9b7c390b0a0c07da6')
    ->setNotifyUrl('https://testpay.jiaheyx.com/paycenter/test.php');

WxPayNotify::init(file_get_contents('php://input'))->handle(false);
