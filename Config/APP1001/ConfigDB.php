<?php

namespace Config\APP1001;

/**
 * Author: skylong
 * CreateTime: 2019-8-12 16:45:32
 * Description: mysql相关配置，本地环境、测试服环境、生成环境
 */
class ConfigDB
{
    const DB_DEFAULT = 1;

    public static function getLocalConfig()
    {
        $config = [
            self::DB_DEFAULT => [
                'host'      => '',
                'username'  => '',
                'password'  => '',
                'dbname'    => '',
                'charset'   => 'utf8',
                'port'      => 3306,
                'db_prefix' => '',
            ],
        ];
    }

    public static function getDevConfig()
    {

    }

    public static function getProConfig()
    {

    }
}