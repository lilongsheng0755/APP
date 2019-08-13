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
     * 默认应用
     */
    const APPID_DEFAULT = 1001;

    /**
     * 应用名配置
     *
     * @var array
     */
    public static $appid = [
        self::APPID_DEFAULT => '默认应用1',
    ];

}
