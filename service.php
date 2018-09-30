<?php

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//设置返回编码
header('Content-Type: text/html;charset=utf-8');
//载入核心文件
require_once 'core.php';

$redis = new Lib\Cache\SRedis('127.0.0.1', 6379);
//$res   = $redis->lPush('mykey', '1');
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
var_dump($redis->lPop('mykey'));
