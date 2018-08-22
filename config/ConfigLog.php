<?php

namespace config;

/**
 * Author: skylong
 * CreateTime: 2018-6-7 11:24:50
 * Description: 日志类型配置，日志保存路径配置，日志存储方式配置
 */
class ConfigLog {

    /**
     * mysql错误日志类型
     */
    const MYSQL_EER_LOG_TYPE = 0;

    /**
     * 根据日志类型获取对应的日志保存路径
     * 
     * @param int $log_type
     * @return string
     */
    public static function getLogPath($log_type = 0) {
        $config = array(
            self::MYSQL_EER_LOG_TYPE => DATA_PATH . DS . 'error_mysql',
        );

        return isset($config[$log_type]) ? $config[$log_type] : DATA_PATH;
    }

}
