<?php

namespace Lib\SPL;
/**
 * Author: skylong
 * CreateTime: 2019-6-11 14:41:59
 * Description: 用于单例模式实例化基类
 */
abstract class SingleBase
{

    /**
     * 单例实例化对象
     *
     * @var array
     */
    protected static $instance = [];

    /**
     * 单例模式实例化
     *
     * @param array $params
     *
     * @return object
     */
    public static function getInstance(...$params)
    {
        $class = get_called_class();
        if (!isset(self::$instance[$class]) || !self::$instance[$class] instanceof $class) {
            self::$instance[$class] = new $class(...$params);
        }
        return self::$instance[$class];
    }

}
