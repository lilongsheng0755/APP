<?php

namespace Lib;

use Lib\System\SException;

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
}
