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
    const MYSQL_LOG_TYPE = 0;

    /**
     * 根据日志类型获取对应的日志保存路径
     * 
     * @param int $logType
     * @return string
     */
    public static function getLogPath($logType = 0) {
        $config = array(
            self::MYSQL_LOG_TYPE => DATA_PATH . DS . 'error_mysql',
        );

        return $config[$logType];
    }

}
