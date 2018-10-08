<?php

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//设置返回编码
header('Content-Type: text/html;charset=utf-8');
//载入核心文件
require_once __DIR__ . '/core.php';

use Lib\Cache\SRedis;

$redis = new SRedis('127.0.0.1');

//var_dump($redis->delete('mykey'));

//var_dump($redis->hSet('mykey', 'hash' . mt_rand(1, 50), mt_rand(1, 50)));
var_dump($redis->hDel('mykey', 'hash200'));
var_dump($redis->hLen('mykey'));
var_dump($redis->hGetAll('mykey'));
var_dump($redis->hKeys('mykey'));
var_dump($redis->hVals('mykey'));
var_dump($redis->hExists('mykey','hash20'));
var_dump($redis->hIncrBy('mykey','hash20',1.5));
var_dump($redis->hMset('mykey',array('ll3'=>10,'llt'=>100)));
var_dump($redis->hMget('mykey',array('ll3','llt','hash20')));
var_dump($redis->exists('mykey0'));

