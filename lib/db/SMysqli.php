<?php

namespace lib\db;

use lib\db\DataBase;
use lib\Log;
use lib\SException;
use helper\HelperReturn;

/**
 * Author: skylong
 * CreateTime: 2018-6-1 16:20:13
 * Description: 基于mysqli扩展的数据库操作类
 */
class SMysqli extends DataBase {

    /**
     * mysqli对象
     *
     * @var \mysqli
     */
    private $mysqli = null;

    /**
     * mysqli_result对象
     *
     * @var \mysqli_result
     */
    private $result = null;

    /**
     * mysqli实例初始化
     * 
     * @param string $host 数据库地址
     * @param string $username 数据库登录用户名
     * @param string $passwd  数据库登录密码
     * @param string $dbname 操作数据库名称
     * @param int $port  数据库端口
     */
    public function __construct($host, $username, $passwd, $dbname, $port = 3306) {
        extension_loaded('mysqli') or die('No mysqli extensions installed');
        $this->mysqli = new \mysqli($host, $username, $passwd, $dbname, $port);
        !$this->mysqli->connect_error or die('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        $this->mysqli->set_charset("utf8") or die("Error loading character set utf8: {$this->mysqli->error}");
    }

    /**
     * 执行一条SQL语句
     * 
     * @param string $sql
     * @return boolean|mysqli_result
     */
    public function query($sql) {
        if (!$this->result = $this->mysqli->query($sql)) {
            $this->writeErrLog($this->mysqli->errno, $this->mysqli->error, $sql);
        }
        return $this->result;
    }

    /**
     * 获取一条记录
     * 
     * @param string $sql
     * @param int $result_type
     * @return array
     */
    public function getOne($sql, $result_type = MYSQLI_ASSOC) {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \mysqli_result)) {
            return array();
        }
        $row = $res->fetch_array($result_type);
        return $row ? $row : array();
    }

    /**
     * 获取多条记录
     * 
     * @param string $sql
     * @param int $result_type
     * @return array
     */
    public function getAll($sql, $result_type = MYSQLI_ASSOC) {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \mysqli_result)) {
            return array();
        }
        $ret = array();
        while ($row = $res->fetch_array($result_type)) {
            $ret[] = $row;
        }
        return $ret ? $ret : array();
    }

    /**
     * 获取一个数据对象
     * 
     * @param string $sql
     * @param int $class_name
     * @return array
     */
    public function getOneObject($sql, $class_name = 'stdClass') {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \mysqli_result)) {
            return array();
        }
        $row = $res->fetch_object($class_name);
        return $row ? $row : array();
    }

    /**
     * 获取多个数据对象
     * 
     * @param string $sql
     * @param int $class_name
     * @return array
     */
    public function getAllObject($sql, $class_name = 'stdClass') {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \mysqli_result)) {
            return array();
        }
        $ret = array();
        while ($row = $res->fetch_object($class_name)) {
            $ret[] = $row;
        }
        return $ret ? $ret : array();
    }

    /**
     * 返回影响行数
     * 
     * @return int
     */
    public function affectedRows() {
        return $this->mysqli->affected_rows;
    }

    /**
     * 返回最新自增ID
     * 
     * @return int
     */
    public function insertID() {
        return $this->mysqli->insert_id;
    }

    /**
     * 开启一个事务,只对InnoDB表起作用
     */
    public function startTransaction() {
        $this->mysqli->autocommit(false);
        $this->mysqli->begin_transaction();
    }

    /**
     * 提交事务
     */
    public function commit() {
        $this->mysqli->commit();
        $this->mysqli->autocommit(true);
    }

    /**
     * 回滚事务
     */
    public function rollback() {
        $this->mysqli->rollback();
        $this->mysqli->autocommit(true);
    }

    /**
     * 获取mysql服务器版本信息
     * 
     * @return string
     */
    public function getServerInfo() {
        return $this->mysqli->server_info;
    }

    /**
     * 获取当前查询返回记录数
     * 
     * @return int
     */
    public function getNumRows() {
        return $this->result->num_rows;
    }

    /**
     * 写操作mysql数据库失败的日志
     * 
     * @param int $errno  错误编号
     * @param int $error  错误信息
     * @param string $query  操作语句
     */
    private function writeErrLog($errno, $error, $query) {
        $e        = new \mysqli_sql_exception();
        $trace    = (array) array_pop($e->getTrace());
        $err_file = (string) $trace['file'] . '(' . (string) $trace['line'] . ')';
        APP_DEBUG && die($err_file . '=======' . $error . '=======' . $query);
        unset($e, $trace);
        $data     = "file:{$err_file}\r\n";
        $data     .= "time:" . date('Y-m-d H:i:s') . "\r\n";
        $data     .= "errno:{$errno}\r\n";
        $data     .= "error:\"{$error}\"\r\n";
        $data     .= "query:\"{$query}\"\r\n";
        $data     .= "======================================================================\r\n";
        Log::writeErrLog('error_mysql' . date('Ymd'), $data);
        HelperReturn::jsonData('DB ERROR!', SException::CODE_MYSQL_ERROR);
    }

    /**
     * 关闭数据库连接，释放结果集内存
     */
    private function __destruct() {
        if ($this->result instanceof \mysqli_result) {
            $this->result->free();
            $this->result->close();
        }

        $this->mysqli->close();
    }

}
