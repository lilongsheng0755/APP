<?php

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//设置返回编码
header('Content-Type: text/json;charset=utf-8');
//载入核心文件
require_once 'core.php';

$mem = new \lib\cache\SMemcached();
var_dump($mem->incr('mykey',100));
