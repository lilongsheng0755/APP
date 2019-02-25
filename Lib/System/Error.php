<?php

namespace Lib\System;

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
     * 注册异常处理
     * @return void
     */
    public static function register() {
        error_reporting(0);
        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']);
        register_shutdown_function([__CLASS__, 'appShutdown']);
    }

    /**
     * 自定义错误处理函数
     * 
     * @param int $error_level  错误级别
     * @param string $error_message  错误信息
     * @param string $file 发生错误文件
     * @param int $line 发生错误的位置
     */
    public static function appError($error_level, $error_message, $file, $line) {
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
            case E_PARSE:
            case E_CORE_ERROR:
            case E_USER_ERROR:
            case E_COMPILE_ERROR:
                $error_type = 'Fatal Error';
                $exit = true;
                break;

            //其他未知错误
            default :
                $error_type = 'Unknown';
        }
        LOCAL && include_once PATH_APP . DS . 'View' . DS . 'debug.tpl';
        self::writeErrLog($error_type, $error_message, $file, $line);
        $exit && HelperReturn::jsonData('PHP ERROR!', SException::CODE_PHP_ERROR);
    }

    /**
     * 异常处理
     * @param  \Exception $e 异常
     * @return void
     */
    public static function appException($e) {
        if (is_object($e)) {
            $trace = $e->getTrace();
            if ($trace) {
                $arr = array_pop($trace);
            } else {
                $arr['file'] = $e->getFile();
                $arr['line'] = $e->getLine();
            }
            LOCAL && include_once PATH_APP . DS . 'View' . DS . 'debug.tpl';
            self::writeErrLog('Exception', $e->getMessage(), $arr['file'], $arr['line']);
            HelperReturn::jsonData('PHP ERROR!', SException::CODE_PHP_ERROR);
        }
    }

    /**
     * 异常中止处理
     * @return void
     */
    public static function appShutdown() {
        //致命错误处理
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) {
            $error_level = $error['type'];
            $error_message = $error['message'];
            $file = $error['file'];
            $line = $error['line'];
            LOCAL && include_once PATH_APP . DS . 'View' . DS . 'debug.tpl';
            self::writeErrLog('Shutdown', $error['message'], $error['file'], $error['line']);
            HelperReturn::jsonData('PHP ERROR!', SException::CODE_PHP_ERROR);
        }
    }

    /**
     * 确定错误类型是否致命
     * @param  int $type 错误类型
     * @return bool
     */
    private static function isFatal($type) {
        return in_array($type, [E_ERROR, E_USER_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
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
        $data .= "time:" . date('Y-m-d H:i:s') . "\r\n";
        $data .= "error_type:{$error_type}\r\n";
        $data .= "error_message:" . $error_message . "\r\n";
        $data .= "======================================================================\r\n";
        Log::writeErrLog('error_php' . date('Ymd'), $data, ConfigLog::PHP_ERR_LOG_TYPE);
    }

}
