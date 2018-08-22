<?php

namespace lib\db;

use lib\Log;

/**
 * Author: skylong
 * CreateTime: 2018-8-20 18:28:05
 * Description: 数据库抽象类
 */
abstract class DataBase {

    abstract protected function insert();

    abstract protected function delete();

    abstract protected function update();

    abstract protected function select();

    abstract protected function query($query);

    abstract protected function affectedRows();
    
    abstract protected function startTransaction();
    
    abstract protected function commit();
    
    abstract protected function rollback();

    /**
     * 写操作mysql数据库失败的日志
     * 
     * @param string $err_file  操作mysql失败的文件
     * @param int $errno  错误编号
     * @param int $error  错误信息
     * @param string $query  操作语句
     */
    protected function writeLog($err_file, $errno, $error, $query) {
        $data = "file:{$err_file}.php\r\n";
        $data .= "time:" . date('Y-m-d H:i:s') . "\r\n";
        $data .= "errno:{$errno}\r\n";
        $data .= "error:\"{$error}\"\r\n";
        $data .= "query:\"{$query}\"\r\n";
        $data .= "======================================================================\r\n";
        Log::writeLog('error_mysql' . date('Ymd'), $data);
    }

}
