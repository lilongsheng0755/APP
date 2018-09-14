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