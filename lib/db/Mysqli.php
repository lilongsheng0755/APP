<?php

namespace lib\db;

use lib\db\DataBase;

/**
 * Author: skylong
 * CreateTime: 2018-6-1 16:20:13
 * Description: 基于mysqli扩展的数据库操作类
 */
class Mysqli extends DataBase {

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
     * mysql实例初始化
     * 
     * @param string $host 数据库地址
     * @param string $username 数据库登录用户名
     * @param string $passwd  数据库登录密码
     * @param string $dbname 操作数据库名称
     * @param int $port  数据库端口
     */
    public function __construct($host, $username, $passwd, $dbname, $port = 3306) {
        class_exists('mysqli') or die('No mysqli extensions installed');
        $this->mysqli = new \mysqli($host, $username, $passwd, $dbname, $port);
        !$this->mysqli->connect_error or die('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        !$this->mysqli->set_charset("utf-8") or die("Error loading character set utf-8: {$this->mysqli->error}\n");
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
     * 开启一个事务
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
     * 获取当前设字符编码
     * 
     * @return array
     */
    public function getCharset() {
        return (array) $this->mysqli->get_charset();
    }

    /**
     * 设置字符编码
     * 
     * @param string $charset
     * @return boolean
     */
    public function setCharset($charset) {
        return $this->mysqli->set_charset($charset);
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
     * 一次性获取所有结果
     * 
     * @param int $result_type
     * @return array
     */
    public function fetchAll($result_type = MYSQLI_ASSOC) {
        return $this->result->fetch_all($result_type);
    }

    /**
     * 以数组的方式获取一条记录
     * 
     * @param int $result_type
     * @return array
     */
    public function fetchArray($result_type = MYSQLI_ASSOC) {
        return $this->result->fetch_array($result_type);
    }

    /**
     * 以字段名格式返回一条数据
     * 
     * @return array
     */
    public function fetchAssoc() {
        return $this->result->fetch_assoc();
    }

    /**
     * 以索引格式返回一条数据
     * 
     * @return array
     */
    public function fetchRow() {
        return $this->result->fetch_row();
    }

    /**
     * 以对象格式返回一条数据
     * 
     * @param string $class_name  自定义类名
     * @return object
     */
    public function fetchObject($class_name = "stdClass") {
        return $this->result->fetch_object($class_name);
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
