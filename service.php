<?php

//载入核心文件
require_once 'core.php';

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//设置返回编码
header('Content-Type: text/html;charset=utf-8');

//APP初始化
use lib\Application;

Application::init();

use lib\db\MongoDB;

$host     = '192.168.0.102';
$username = 'test';
$passwd   = 'test';
$dbname   = 'test';
$port     = 27018;
$mongo    = new MongoDB($host, $username, $passwd, $dbname, $port);
echo '<pre>';
var_dump($mongo->getObjectID());

