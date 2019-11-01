<?php
/**
 * Author: skylong
 * CreateTime: 2018-6-1 16:20:13
 * Description: 应用初始化入口
 */

namespace Lib;

use Config\ConfigApp;

class Application
{

    /**
     * 应用初始化
     */
    public static function init()
    {
        // 自动加载项目文件
        spl_autoload_register('self::autoload');

        // 初始化常量
        self::setConstVal();
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
            include_once($file);
        }
        return true;
    }

    /**
     * 设置系统常量
     *
     * @return bool
     */
    public static function setConstVal()
    {
        $appid = isset($_SESSION['appid']) ? (int)$_SESSION['appid'] : (isset($_REQUEST['appid']) ? (int)trim($_REQUEST['appid']) : 0);
        define('APPID', $appid); // 设置项目APPID常量
        if (key_exists($appid, ConfigApp::$map_appid_names)) {
            define('PROJECT_NS', 'APP' . $appid); // 项目缓存key前缀
        }

        if (isset($_REQUEST['sss'])) {
            define('REQUEST_SOURCE', 1); // 来至页面请求
        } elseif (isset($_POST['post_data'])) {
            define('REQUEST_SOURCE', 2); // 来至API请求
        } else {
            header('HTTP/1.1 404 Not Found');
            exit();
        }
        return true;
    }
}
