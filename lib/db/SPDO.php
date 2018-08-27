<?php

namespace lib\db;

use lib\db\DataBase;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:20:22
 * Description: 基于pdo扩展的数据库操作类
 */
class SPDO extends DataBase {

    /**
     * pdo对象
     *
     * @var \pdo
     */
    private $pdo = null;

    /**
     * pdo_result对象
     *
     * @var \pdo_result
     */
    private $result = null;

    /**
     * mysql实例初始化
     * 
     * @param string $dsn 数据库地址
     * @param string $username 数据库登录用户名
     * @param string $passwd  数据库登录密码
     */
    public function __construct($dsn, $username, $passwd) {
        class_exists('PDO') or die('No pdo extensions installed');
        $driver_options = array(
            \PDO::ATTR_CASE                     => \PDO::CASE_NATURAL, //保留数据库驱动返回的列名。 
            \PDO::ATTR_ERRMODE                  => \PDO::ERRMODE_EXCEPTION, // 抛出 exceptions 异常
            \PDO::ATTR_ORACLE_NULLS             => \PDO::NULL_TO_STRING, //将 NULL 转换成空字符串
            \PDO::ATTR_AUTOCOMMIT               => true, //是否自动提交每个单独的语句
            \PDO::ATTR_EMULATE_PREPARES         => true, //启用或禁用预处理语句的模拟
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false, // 使用缓冲查询
            \PDO::ATTR_DEFAULT_FETCH_MODE       => \PDO::FETCH_ASSOC, // 设置默认的提取模式
        );
        try {
            $this->pdo = new \PDO($dsn, $username, $passwd, $driver_options);
        } catch (\PDOException $e) {
            if (DEBUG) {
                die('Connection failed: ' . $e->getMessage());
            } else {
                die('Connection failed!');
            }
        }
    }

    /**
     * 执行一条SQL语句
     * 
     * @param string $sql
     * @return boolean|pdo_result
     */
    public function query($sql) {
        if (!$this->result = $this->pdo->query($sql)) {
            $this->writeErrLog($this->pdo->errno, $this->pdo->error, $sql);
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
        if (!$res || !($res instanceof \pdo_result)) {
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
        if (!$res || !($res instanceof \pdo_result)) {
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
        if (!$res || !($res instanceof \pdo_result)) {
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
        if (!$res || !($res instanceof \pdo_result)) {
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
        return $this->pdo->affected_rows;
    }

    /**
     * 返回最新自增ID
     * 
     * @return int
     */
    public function insertID() {
        return $this->pdo->insert_id;
    }

    /**
     * 开启一个事务,只对InnoDB表起作用
     */
    public function startTransaction() {
        $this->pdo->autocommit(false);
        $this->pdo->begin_transaction();
    }

    /**
     * 提交事务
     */
    public function commit() {
        $this->pdo->commit();
        $this->pdo->autocommit(true);
    }

    /**
     * 回滚事务
     */
    public function rollback() {
        $this->pdo->rollback();
        $this->pdo->autocommit(true);
    }

    /**
     * 获取mysql服务器版本信息
     * 
     * @return string
     */
    public function getServerInfo() {
        return $this->pdo->server_info;
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
        $e        = new \pdo_sql_exception();
        $trace    = (array) array_pop($e->getTrace());
        $err_file = (string) $trace['file'] . '(' . (string) $trace['line'] . ')';
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

    /**
     * 关闭数据库连接，释放结果集内存
     */
    private function __destruct() {
        if ($this->result instanceof \pdo_result) {
            $this->result->free();
            $this->result->close();
        }

        $this->pdo->close();
    }

}
