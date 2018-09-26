<?php

//设置错误级别，脚本运行时间设置
//error_reporting(0);
set_time_limit(30);

//定义入口常量
define('IN_APP', true);

//项目名称
define('PROJECT_NS', 'app');

//本地环境变量
define('LOCAL', true);

//生产环境变量
define('PRODUCTION_ENV', false);

//是否开启debug模式
define('APP_DEBUG', true);

//设置市区
date_default_timezone_set('Asia/Shanghai');

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

//静态资源路径
define('PUBLIC_PATH', APP_PATH . DS . 'static');

//加载初始化文件
require_once LIB_PATH . '/Application.php';

//APP初始化
lib\Application::init();

