<?php

//脚本运行时间设置
set_time_limit(30);

/*
 * 设置市区
 */
date_default_timezone_set('Asia/Shanghai');

/*
 * 定义入口常量
 */
define('IN_APP', true);

/*
 * 项目名称
 */
define('PROJECT_NS', 'APP');

/*
 * 是否开启调试模式【生产环境要改回false】
 */
define('APP_DEBUG', true);

/*
 * 生产环境常量
 */
define('PRODUCTION_ENV', false);

/*
 * 环境常量cli为命令行执行
 */
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);

/**
 * 简化目录分隔符
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * 设置应用基本路径
 */
define('PATH_APP', __DIR__);

/**
 * 库文件基路径
 */
define('PATH_LIB', PATH_APP . DS . 'Lib');

/**
 * 日志文件基路径
 */
define('PATH_DATA', PATH_APP . DS . 'Data');

/**
 * 文件上传基路径
 */
define('PATH_UPLOAD', PATH_APP . DS . 'Upload');

/**
 * 静态资源基路径
 */
define('PATH_PUBLIC', PATH_APP . DS . 'Static');

/**
 * 第三方插件基路径
 */
define('PATH_PLUGS', PATH_APP . DS . 'Plugs');

/**
 * 配置常量：local - 本地环境，dev - 外网测试环境【RC】，product - 生产环境【GA】
 */
define('CONFIG_ENVIRONMENT', 'local');

/**
 * 加载初始化文件
 */
require_once PATH_LIB . '/Application.php';

/**
 * APP初始化
 */
Lib\Application::init();
Lib\System\Error::register();
//Lib\Session\Session::start();
//Lib\Session\DBSession::start($db, $tblname, $primary_key); 使用db存储session
//Lib\Session\MEMSession::start($mem); 使用cache存储session
