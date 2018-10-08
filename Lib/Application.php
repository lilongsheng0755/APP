<?php

namespace Lib;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-6-1 16:20:13
 * Description: 应用初始化入口
 */
class Application {

    /**
     * 应用初始化
     */
    public static function init() {
        spl_autoload_register('self::autoload');
    }

    /**
     * 类自动加载
     * 
     * @param string $class_name  加载类名带上命名空间
     */
    public static function autoload($class_name) {
        $file = APP_PATH . DS . str_replace('\\', DS, $class_name) . '.php';
        try {
            self::loadFile($file);
        } catch (\lib\SException $e) {
            die($e);
        }
    }

    /**
     * 加载文件
     * 
     * @param string $file  文件绝对路径
     * @throws \lib\SException
     */
    public static function loadFile($file) {
        if (!file_exists($file) || !is_file($file)) {
            throw new \lib\SException("{$file} NOT EXSIST!", \lib\SException::CODE_NOT_FOUND_FILE);
        }
        require_once $file;
    }

}
