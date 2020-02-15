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
     * 应用名称配置
     *
     * @var array
     */
    public static $map_appid_names = [
        self::APPID_SYSTEM => '系统应用',
    ];

    /**
     * 获取静态资源URL地址
     *
     * @return string
     */
    public static function getStaticSourceUrl()
    {
        if (defined('STATIC_URL')) {
            return STATIC_URL;
        }
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $domainName = $_SERVER['HTTP_HOST'] . '/';
        return $protocol . $domainName . '/Static/';
    }

}
