<?php

namespace Config;

/**
 * Author: skylong
 * CreateTime: 2018-6-7 11:24:50
 * Description: 日志类型配置，日志保存路径配置，日志存储方式配置
 */
class ConfigLog {

    /**
     * PHP错误日志类型
     */
    const PHP_ERR_LOG_TYPE = 0;

    /**
     * mysql错误日志类型
     */
    const MYSQL_ERR_LOG_TYPE = 1;

    /**
     * MongoDB错误日志类型
     */
    const MONGODB_ERR_LOG_TYPE = 2;

    /**
     * memcached错误日志类型
     */
    const MEM_ERR_LOG_TYPE = 3;

    /**
     * redis错误日志类型
     */
    const REDIS_ERR_LOG_TYPE = 4;

    /**
     * 根据日志类型获取对应的日志保存路径
     * 
     * @param int $log_type
     * @return string
     */
    public static function getLogPath($log_type = 0) {
        $config = array(
            self::PHP_ERR_LOG_TYPE     => PATH_DATA . DS . 'error_php',
            self::MYSQL_ERR_LOG_TYPE   => PATH_DATA . DS . 'error_mysql',
            self::MONGODB_ERR_LOG_TYPE => PATH_DATA . DS . 'error_mongodb',
            self::MEM_ERR_LOG_TYPE     => PATH_DATA . DS . 'error_memcached',
            self::REDIS_ERR_LOG_TYPE   => PATH_DATA . DS . 'error_redis',
        );

        return isset($config[$log_type]) ? $config[$log_type] : PATH_DATA;
    }

}
