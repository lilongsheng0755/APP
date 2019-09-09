<?php

namespace Config;

/**
 * Author: skylong
 * CreateTime: 2018-6-11 18:26:43
 * Description: 应用相关配置
 */
class ConfigApp
{
    /**
     * 默认应用【系统应用】
     */
    const APPID_SYSTEM = 'System';

    /**
     * 应用名配置
     *
     * @var array
     */
    public static $appid = [
        self::APPID_SYSTEM => '系统应用',
    ];

}
