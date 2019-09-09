<?php

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//载入核心文件
require_once __DIR__ . '/core.php';

// 设置微信支付配置
use Lib\WxPay\PayConfig\WxPayConfig;

WxPayConfig::getInstance()->setMerchantId();
WxPayConfig::getInstance()->setAppId();
WxPayConfig::getInstance()->setSignType('MD5');
WxPayConfig::getInstance()->setPayKey();
WxPayConfig::getInstance()->setAppSecret();
WxPayConfig::getInstance()->setNotifyUrl();