<?php

namespace Config\System;
use Lib\SPL\SplInterface\IConfigDB;

/**
 * Author: skylong
 * CreateTime: 2019-8-12 16:45:32
 * Description: mysql相关配置，本地环境、测试服环境、生成环境
 */
class ConfigDB implements IConfigDB
{

    /**
     * 本地环境DB配置
     *
     * @param int $db_index
     *
     * @return mixed
     */
    public static function getLocalConfig($db_index = self::DB_INDEX_SYSTEM)
    {
        $config = [
            self::DB_INDEX_SYSTEM => [
                'host'      => '192.168.1.49',
                'username'  => 'dev',
                'password'  => 'dev',
                'dbname'    => '',
                'charset'   => 'utf8',
                'port'      => 3306,
                'db_prefix' => '',
            ],
        ];
        return isset($config[$db_index]) ? $config[$db_index] : $config[self::DB_INDEX_SYSTEM];
    }

    /**
     * 外网测试环境DB配置
     *
     * @param int $db_index
     *
     * @return mixed
     */
    public static function getDevConfig($db_index = self::DB_INDEX_SYSTEM)
    {
        // TODO: Implement getDevConfig() method.
    }

    /**
     * 生产环境DB配置
     *
     * @param int $db_index
     *
     * @return mixed
     */
    public static function getProConfig($db_index = self::DB_INDEX_SYSTEM)
    {
        // TODO: Implement getProConfig() method.
    }
}
