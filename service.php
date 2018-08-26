<?php

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//设置返回编码
header('Content-Type: text/html;charset=utf-8');

//载入核心文件
require_once 'core.php';

//APP初始化
use lib\Application;
use lib\db\Mysqli;

Application::init();
$db = new Mysqli('192.168.0.102', 'test', 'test', 'test');
$sql = "select * from test";
$res = $db->getAll($sql);
echo '<pre>';
var_dump($db->getNumRows());

