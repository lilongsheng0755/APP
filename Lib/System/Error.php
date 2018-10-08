<?php

namespace Lib\System;

defined('IN_APP') or die('Access denied!');

use Lib\System\Log;
use Lib\System\SException;
use Config\ConfigLog;
use Helper\HelperReturn;

/**
 * Author: skylong
 * CreateTime: 2018-10-8 22:23:10
 * Description: 自定义PHP错误管理类
 */
class Error {

    /**
     * 自定义错误处理函数
     * 
     * @param int $error_level  错误级别
     * @param string $error_message  错误信息
     * @param string $file 发生错误文件
     * @param int $line 发生错误的位置
     */
    public static function errorHandler($error_level, $error_message, $file, $line) {
        $exit = false;
        switch ($error_level) {
            //提醒级别
            case E_NOTICE:
            case E_USER_NOTICE:
                $error_type = 'Notice';
                break;

            //警告级别
            case E_WARNING:
            case E_USER_WARNING:
                $error_type = 'Warning';
                break;

            //错误级别
            case E_ERROR:
            case E_USER_ERROR:
                $error_type = 'Fatal Error';
                $exit       = true;
                break;

            //其他未知错误
            default :
                $error_type = 'Unknown';
                $exit       = true;
        }
        self::writeErrLog($error_type, $error_message, $file, $line);
        ob_clean();
        $exit && HelperReturn::jsonData('PHP ERROR!', SException::CODE_PHP_ERROR);
    }

    /**
     * PHP错误信息写入文件
     * 
     * @param string $error_type  错误类型
     * @param string $error_message  错误信息
     * @param string $file 发生错误文件
     * @param int $line 发生错误的位置
     */
    private static function writeErrLog($error_type, $error_message, $file, $line) {
        $data = "file:{$file}({$line})\r\n";
        $data .= "error_type:{$error_type}\r\n";
        $data .= "error_message:" . $error_message . "\r\n";
        $data .= "======================================================================\r\n";
        Log::writeErrLog('error_php' . date('Ymd'), $data, ConfigLog::PHP_ERR_LOG_TYPE);
    }

}
