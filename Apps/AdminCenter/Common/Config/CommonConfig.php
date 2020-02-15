<?php

namespace Apps\AdminCenter\Common\Config;

use Config\ConfigApp;

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