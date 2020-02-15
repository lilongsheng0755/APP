<?php

namespace Apps\AdminCenter\Common\Config;

use Config\ConfigApp;

/**
 * Author: skylong
 * CreateTime: 2020/2/15 10:12
 * Description: 后台中心公共配置
 */
class CommonConfig
{
    /**
     * 设置常量
     */
    public static function setConst()
    {
        // 静态资源常量
        defined('STATIC_URL') or define('STATIC_URL', ConfigApp::getStaticSourceUrl());
    }
}