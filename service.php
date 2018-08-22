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
use lib\Log;
Application::init();
$db = new Mysqli('192.168.0.102', 'test', 'test', 'test');
$db->startTransaction();
var_dump($db->query("INSERT INTO `test` SET title='文章标题',`contents`='文章内容'"));
var_dump($db->query("INSERT INTO `test` SET title='文章标题',`contents`='文章内容'"));
$db->rollback();
