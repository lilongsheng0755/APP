<?php

namespace Lib\DB;

use Lib\DB\DataBase;
use Lib\System\Log;
use Lib\System\SException;
use Config\ConfigLog;
use Helper\HelperReturn;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:20:22
 * Description: 基于pdo扩展的数据库操作类
 */
class SPDO extends DataBase
{

    /**
     * mysql数据库地址
     *
     * @var string
     */
    private $host;

    /**
     * 登录数据库用户名
     *
     * @var string
     */
    private $username;

    /**
     * 登录数据库密码
     *
     * @var string
     */
    private $passwd;

    /**
     * 当前操作的数据库名
     *
     * @var string
     */
    private $dbname;

    /**
     * 访问端口
     *
     * @var int
     */
    private $port;

    /**
     * 驱动操作项
     *
     * @var array
     */
    private $driver_options;

    /**
     * pdo对象
     *
     * @var \pdo
     */
    private $pdo = null;

    /**
     * PDOStatement对象
     *
     * @var \PDOStatement
     */
    private $result = null;

    /**
     * 当前查询返回记录数
     *
     * @var int
     */
    private $num_rows = 0;

    /**
     * 初始化参数
     *
     * @param string $host     数据库地址
     * @param string $username 数据库登录用户名
     * @param string $passwd   数据库登录密码
     * @param string $dbname   操作数据库名称
     * @param int    $port     数据库端口
     */
    public function __construct($host, $username, $passwd, $dbname, $port = 3306)
    {
        extension_loaded('pdo_mysql') or die('No pdo extensions installed');
        $driver_options = [
            \PDO::ATTR_CASE                     => \PDO::CASE_NATURAL, //保留数据库驱动返回的列名。 
            \PDO::ATTR_ERRMODE                  => \PDO::ERRMODE_EXCEPTION, // 抛出 exceptions 异常
            \PDO::ATTR_ORACLE_NULLS             => \PDO::NULL_TO_STRING, //将 NULL 转换成空字符串
            \PDO::ATTR_AUTOCOMMIT               => true, //是否自动提交每个单独的语句
            \PDO::ATTR_EMULATE_PREPARES         => true, //启用或禁用预处理语句的模拟
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false, // 使用缓冲查询
            \PDO::ATTR_DEFAULT_FETCH_MODE       => \PDO::FETCH_ASSOC, // 设置默认的提取模式
            \PDO::ATTR_PERSISTENT               => false, // 持久化连接
        ];
        $this->host = $host;
        $this->username = $username;
        $this->passwd = $passwd;
        $this->dbname = $dbname;
        $this->port = $port;
        $this->driver_options = $driver_options;
    }

    /**
     * 创建数据库连接对象
     *
     * @return \PDO
     */
    private function connect()
    {
        if ($this->pdo instanceof \PDO) {
            return $this->pdo;
        }
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            $this->pdo = new \PDO($dsn, $this->username, $this->passwd, $this->driver_options);
        } catch (\PDOException $e) {
            if (!PRODUCTION_ENV) {
                die('Connection failed: ' . $e->getMessage());
            } else {
                die('Connection failed!');
            }
        }
        return $this->pdo;
    }

    /**
     * 执行一条SQL语句
     *
     * @param string $sql sql语句
     *
     * @return boolean|\PDOStatement
     */
    public function query($sql)
    {
        $this->connect();
        try {
            $this->result = $this->pdo->query($sql);
        } catch (\PDOException $ex) {
            $arr = (array)$ex->getTrace();
            $trace = (array)array_pop($arr);
            $err_file = (string)$trace['file'] . '(' . (string)$trace['line'] . ')';
            $this->writeErrLog($err_file, $ex->getCode(), $ex->getMessage(), $sql);
        }
        return $this->result;
    }

    /**
     * 获取一条记录
     *
     * @param string $sql         sql语句
     * @param int    $result_type 返回类型，默认为关联数组
     *
     * @return array
     */
    public function getOne($sql, $result_type = \PDO::FETCH_ASSOC)
    {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \PDOStatement)) {
            return [];
        }
        $row = $res->fetch($result_type);
        $row && $this->num_rows = 1;
        $res->closeCursor();
        return $row ? $row : [];
    }

    /**
     * 获取多条记录
     *
     * @param string $sql         sql语句
     * @param int    $result_type 返回类型，默认为关联数组
     *
     * @return array
     */
    public function getAll($sql, $result_type = \PDO::FETCH_ASSOC)
    {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \PDOStatement)) {
            return [];
        }
        $ret = [];
        while ($row = $res->fetch($result_type)) {
            $ret[] = $row;
            $this->num_rows++;
        }
        $res->closeCursor();
        return $ret ? $ret : [];
    }

    /**
     * 获取一个数据对象
     *
     * @param string $sql        sql语句
     * @param string $class_name 类名
     *
     * @return array
     */
    public function getOneObject($sql, $class_name = '\stdClass')
    {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \PDOStatement)) {
            return [];
        }
        $row = $res->fetchObject($class_name);
        $row && $this->num_rows = 1;
        $res->closeCursor();
        return $row ? $row : [];
    }

    /**
     * 获取多个数据对象
     *
     * @param string $sql        sql语句
     * @param string $class_name 类名
     *
     * @return array
     */
    public function getAllObject($sql, $class_name = '\stdClass')
    {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \PDOStatement)) {
            return [];
        }
        $ret = [];
        while ($row = $res->fetchObject($class_name)) {
            $ret[] = $row;
            $this->num_rows++;
        }
        $res->closeCursor();
        return $ret ? $ret : [];
    }

    /**
     * 返回影响行数
     *
     * @return int
     */
    public function affectedRows()
    {
        return $this->result->rowCount();
    }

    /**
     * 返回最新自增ID
     *
     * @return int
     */
    public function insertID()
    {
        $this->connect();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * 开启一个事务,只对InnoDB表起作用
     */
    public function startTransaction()
    {
        $this->connect();
        $this->pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
        $this->pdo->beginTransaction();
    }

    /**
     * 执行一条 SQL 语句，并返回受影响的行数
     *
     * @param string $sql sql语句
     *
     * @return int 返回影响的行数
     */
    public function exec($sql)
    {
        $this->connect();
        try {
            return $this->pdo->exec($sql);
        } catch (\PDOException $ex) {
            $arr = (array)$ex->getTrace();
            $trace = (array)array_pop($arr);
            $err_file = (string)$trace['file'] . '(' . (string)$trace['line'] . ')';
            $this->writeErrLog($err_file, $ex->getCode(), $ex->getMessage(), $sql);
        }
        return true;
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        $this->connect();
        $this->pdo->commit();
        $this->pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->connect();
        $this->pdo->rollBack();
        $this->pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);
    }

    /**
     * 获取当前查询返回记录数
     *
     * @return int
     */
    public function getNumRows()
    {
        return $this->num_rows;
    }

    /**
     * 执行一条预处理语句
     *
     * @param string $sql     预处理语句
     * @param array  $prepare 需要绑定的参数
     *
     * @return boolean
     */
    public function execPrepare($sql, $prepare = [])
    {
        $this->connect();
        try {
            $this->result = $this->pdo->prepare($sql);
            return $this->result->execute($prepare);
        } catch (\PDOException $ex) {
            $arr = (array)$ex->getTrace();
            $trace = (array)array_pop($arr);
            $err_file = (string)$trace['file'] . '(' . (string)$trace['line'] . ')';
            $this->writeErrLog($err_file, $ex->getCode(), $ex->getMessage(), $sql);
        }
        return $this->result;
    }

    /**
     * 写操作mysql数据库失败的日志
     *
     * @param int    $err_file 发生错误的位置
     * @param int    $errno    错误编号
     * @param int    $error    错误信息
     * @param string $query    操作语句
     */
    private function writeErrLog($err_file, $errno, $error, $query)
    {
        !PRODUCTION_ENV && die($err_file . '=======' . $error . '=======' . $query);
        $data = "file:{$err_file}" . PHP_EOL;
        $data .= "time:" . date('Y-m-d H:i:s') . PHP_EOL;
        $data .= "errno:{$errno}" . PHP_EOL;
        $data .= "error:{$error}" . PHP_EOL;
        $data .= "query:{$query}" . PHP_EOL;
        $data .= "======================================================================" . PHP_EOL;
        Log::writeErrLog('error_mysql' . date('Ymd'), $data, ConfigLog::MYSQL_ERR_LOG_TYPE);
        HelperReturn::jsonData('DB ERROR!', SException::CODE_MYSQL_ERROR);
    }

}
