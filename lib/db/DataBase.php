<?php

namespace lib\db;

use lib\Log;

/**
 * Author: skylong
 * CreateTime: 2018-8-20 18:28:05
 * Description: 数据库抽象类
 */
abstract class DataBase {
    abstract protected function query($sql);

    abstract protected function affectedRows();

    abstract protected function startTransaction();

    abstract protected function commit();

    abstract protected function rollback();

    abstract protected function insertID();

    abstract protected function getServerInfo();

    abstract protected function getCharset();

    abstract protected function setCharset($charset);

    /**
     * 写操作mysql数据库失败的日志
     * 
     * @param int $errno  错误编号
     * @param int $error  错误信息
     * @param string $query  操作语句
     */
    protected function writeErrLog($errno, $error, $query) {
        $e        = new \mysqli_sql_exception();
        $trace    = $e->getTrace();
        $err_file = (string) $trace[1]['file'] . '(' . (string) $trace[1]['line'] . ')';
        DEBUG && die($err_file . '=======' . $error . '=======' . $query);
        unset($e, $trace);
        $data     = "file:{$err_file}.php\r\n";
        $data     .= "time:" . date('Y-m-d H:i:s') . "\r\n";
        $data     .= "errno:{$errno}\r\n";
        $data     .= "error:\"{$error}\"\r\n";
        $data     .= "query:\"{$query}\"\r\n";
        $data     .= "======================================================================\r\n";
        Log::writeErrLog('error_mysql' . date('Ymd'), $data);
        PRODUCTION_ENV && die('DB ERROR!');
    }

}
