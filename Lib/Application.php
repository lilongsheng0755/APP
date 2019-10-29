<?php

namespace Lib;

use Config\ConfigApp;

/**
 * Author: skylong
 * CreateTime: 2018-6-1 16:20:13
 * Description: 应用初始化入口
 */
class Application
{

    /**
     * 应用初始化
     */
    public static function init()
    {
        // 自动加载项目文件
        spl_autoload_register('self::autoload');
    }

    /**
     * 类自动加载
     *
     * @param string $class_name 加载类名带上命名空间
     *
     * @return bool
     */
    public static function autoload($class_name)
    {
        $file = PATH_APP . DS . str_replace('\\', DS, $class_name) . '.php';
        if (file_exists($file) || is_file($file)) {
            require_once $file;
        }
        return true;
    }

    /**
     * 设置系统常量
     */
    public static function setConstVal()
    {
        $appid = $_SESSION['appid'] ? (int)$_SESSION['appid'] : (int)trim($_REQUEST['appid']);
        define('APPID', $appid); // 设置项目APPID常量
        if (key_exists($appid, ConfigApp::$map_appid_names)) {
            define('PROJECT_NS', 'APP' . $appid); // 项目缓存key前缀
        }
    }

    public static function route(){
        
    }
}
