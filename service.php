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
use lib\db\SPDO;

Application::init();
$db = new SPDO('mysql:dbname=test;host=192.168.0.102' , 'test', 'test');
$res = $db->execPrepare("insert into test set title=?,contents=?",array(1000,222));
echo '<pre>';
var_dump($res,$db->affectedRows(),$db->insertID());

