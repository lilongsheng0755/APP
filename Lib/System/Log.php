<?php

namespace Lib\System;

defined('IN_APP') or die('Access denied!');

use Config\ConfigLog;

/**
 * Author: skylong
 * CreateTime: 2018-6-1 16:20:13
 * Description: 日志操作类(业务操作日志和系统错误日志)
 */
class Log {

    /**
     * 写入日志文件
     * 
     * @param string $file_name 日志文件名
     * @param string $data  日志信息
     * @param int $log_type  日志类型
     * @param int $limit  文件大小限制，默认为1MB
     */
    public static function writeErrLog($file_name, $data, $log_type = ConfigLog::MYSQL_EER_LOG_TYPE, $limit = 1048576) {
        $log_dir = ConfigLog::getLogPath($log_type);
        if (!is_dir($log_dir) || !file_exists($log_dir)) {
            mkdir($log_dir, 0744, true);
        }
        $file_name = rtrim($log_dir, DS) . DS . $file_name;
        $file_size = file_exists($file_name) ? filesize($file_name) : 0;
        $flag      = $file_size >= $limit ? 0 : FILE_APPEND | LOCK_EX;
        file_put_contents($file_name, $data, $flag);
    }

}
