<?php
header('Content-Type:text/html; charset=utf-8');

// 定义基础路径常量
define('WEB_ROOT',str_replace('Public','',__DIR__));

// 载入核心文件
require_once WEB_ROOT . 'core.php';

// 路由初始化
\Lib\Request\Route::getInstance()->init();