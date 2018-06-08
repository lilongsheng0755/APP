<?php

//过滤错误信息
//error_reporting(0);

//定义入口常量
define('IN_APP', true);

//设置市区
date_default_timezone_set('Asia/Shanghai');

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//设置返回编码
header('charset:utf-8');

//简化目录分隔符
define('DS', DIRECTORY_SEPARATOR);

//设置应用基本路径
define('APP_PATH', __DIR__);

//设置库文件路径
define('LIB_PATH', APP_PATH . DS . 'lib');

//日志文件路径配置
define('DATA_PATH', APP_PATH . DS . 'data');

//文件上传路径
define('UPLOAD_PATH', APP_PATH . DS . 'upload');

//加载初始化文件
require_once LIB_PATH . '/Application.php';

