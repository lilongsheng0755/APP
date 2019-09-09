<?php

namespace Config;

/**
 * Author: skylong
 * CreateTime: 2018-6-7 11:24:50
 * Description: 日志类型配置，日志保存路径配置，日志存储方式配置
 */
class ConfigLog
{

    /**
     * PHP错误日志类型
     */
    const ERR_PHP_LOG_TYPE = 0;

    /**
     * mysql错误日志类型
     */
    const ERR_MYSQL_LOG_TYPE = 1;

    /**
     * MongoDB错误日志类型
     */
    const ERR_MONGODB_LOG_TYPE = 2;

    /**
     * memcached错误日志类型
     */
    const ERR_MEM_LOG_TYPE = 3;

    /**
     * redis错误日志类型
     */
    const ERR_REDIS_LOG_TYPE = 4;

    /**
     * curl错误日志类型
     */
    const ERR_CURL_LOG_TYPE = 5;

    /**
     * 微信支付回调错误日志类型
     */
    const ERR_WXPAY_CALLBACK_LOG_TYPE = 6;

    /**
     * 微信支付接口响应错误日志类型
     */
    const ERR_WXPAY_RETURN_LOG_TYPE = 7;

    /**
     * 根据日志类型获取对应的日志保存路径
     *
     * @param int $log_type
     *
     * @return string
     */
    public static function getLogPath($log_type = 0)
    {
        $config = [
            self::ERR_PHP_LOG_TYPE            => PATH_DATA . DS . 'error_php',
            self::ERR_MYSQL_LOG_TYPE          => PATH_DATA . DS . 'error_mysql',
            self::ERR_MONGODB_LOG_TYPE        => PATH_DATA . DS . 'error_mongodb',
            self::ERR_MEM_LOG_TYPE            => PATH_DATA . DS . 'error_memcached',
            self::ERR_REDIS_LOG_TYPE          => PATH_DATA . DS . 'error_redis',
            self::ERR_CURL_LOG_TYPE           => PATH_DATA . DS . 'error_curl',
            self::ERR_WXPAY_CALLBACK_LOG_TYPE => PATH_DATA . DS . 'error_wxpay_callback',
            self::ERR_WXPAY_RETURN_LOG_TYPE   => PATH_DATA . DS . 'error_wxpay_return',
        ];
        return isset($config[$log_type]) ? $config[$log_type] : PATH_DATA;
    }

}
