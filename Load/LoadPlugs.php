<?php

namespace Load;

/**
 * Author: skylong
 * CreateTime: 2018-10-14 18:47:42
 * Description: 工厂模式实例插件类对象
 */
class LoadPlugs {

    /**
     * 用于存储实例对象
     *
     * @var array
     */
    private static $instance = array();

    /**
     * smarty对象
     * 
     * @return \Smarty
     */
    public static function smarty() {
        include_once PATH_PLUGS . DS . 'Smarty' . DS . 'Smarty.class.php';
        if (isset(self::$instance[__FUNCTION__]) && self::$instance[__FUNCTION__] instanceof \Smarty) {
            return self::$instance[__FUNCTION__];
        }
        self::$instance[__FUNCTION__] = new \Smarty();
        return self::$instance[__FUNCTION__];
    }

}
