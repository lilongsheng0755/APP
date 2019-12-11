<?php
/**
 * Author: skylong
 * CreateTime: 2019/12/11 11:01
 * Description: 实现反射类实例化
 */

namespace Lib\SPL\SplAbstract;


abstract class AReflectionClass
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
     * @return mixed
     */
    public static function getReflectionClass()
    {
        try {
            $key = md5(static::class);
            if (!isset(self::$instance[$key]) || !self::$instance[$key] instanceof \ReflectionClass) {
                self::$instance[$key] = new \ReflectionClass(static::class);
            }
            return self::$instance[$key];
        } catch (\ReflectionException $e) {
            return new \stdClass();
        }

    }

}