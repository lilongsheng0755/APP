<?php

namespace Lib\DB;

use Lib\DB\DataBase;
use Lib\System\Log;
use Lib\System\SException;
use Config\ConfigLog;
use Helper\HelperReturn;

/**
 * Author: skylong
 * CreateTime: 2018-6-1 16:20:13
 * Description: 基于php mysqli扩展的数据库操作类
 */
class SMysqli extends DataBase
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
     * 数据库编码
     *
     * @var string
     */
    private $charset;

    /**
     * 访问端口
     *
     * @var int
     */
    private $port;

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
     * 初始化参数
     *
     * @param string $host     数据库地址
     * @param string $username 数据库登录用户名
     * @param string $passwd   数据库登录密码
     * @param string $dbname   操作数据库名称
     * @param int    $port     数据库端口
     */
    public function __construct($host, $username, $passwd, $dbname, $charset = 'utf8', $port = 3306)
    {
        extension_loaded('mysqli') or die('No mysqli extensions installed');
        $this->host = $host;
        $this->username = $username;
        $this->passwd = $passwd;
        $this->dbname = $dbname;
        $this->port = $port;
        $this->charset = $charset;
    }

    /**
     * 创建数据库连接对象
     *
     * @return \mysqli
     */
    private function connect()
    {
        if ($this->mysqli instanceof \mysqli) {
            return $this->mysqli;
        }
        $this->mysqli = new \mysqli($this->host, $this->username, $this->passwd, $this->dbname, $this->port);
        !$this->mysqli->connect_error or die('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        ($this->charset && $this->mysqli->set_charset($this->charset)) or die("Error loading character set utf8: {$this->mysqli->error}");
        return $this->mysqli;
    }

    /**
     * 执行一条SQL语句
     *
     * @param string $sql
     *
     * @return boolean|\mysqli_result
     */
    public function query($sql)
    {
        $this->connect();
        if (!$this->result = $this->mysqli->query($sql)) {
            $this->writeErrLog($this->mysqli->errno, $this->mysqli->error, $sql);
        }
        return $this->result;
    }

    /**
     * 获取一条记录
     *
     * @param string $sql
     * @param int    $result_type
     *
     * @return array
     */
    public function getOne($sql, $result_type = MYSQLI_ASSOC)
    {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \mysqli_result)) {
            return [];
        }
        $row = $res->fetch_array($result_type);
        $res->free();
        return $row ? $row : [];
    }

    /**
     * 获取多条记录
     *
     * @param string $sql
     * @param int    $result_type
     *
     * @return array
     */
    public function getAll($sql, $result_type = MYSQLI_ASSOC)
    {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \mysqli_result)) {
            return [];
        }
        $ret = [];
        while ($row = $res->fetch_array($result_type)) {
            $ret[] = $row;
        }
        $res->free();
        return $ret ? $ret : [];
    }

    /**
     * 获取一个数据对象
     *
     * @param string $sql
     * @param string $class_name
     *
     * @return object|\stdClass
     */
    public function getOneObject($sql, $class_name = '\stdClass')
    {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \mysqli_result)) {
            return (new \stdClass());
        }
        $row = $res->fetch_object($class_name);
        $res->free();
        return $row ? $row : (new \stdClass());
    }

    /**
     * 获取多个数据对象
     *
     * @param string $sql
     * @param string $class_name
     *
     * @return array
     */
    public function getAllObject($sql, $class_name = '\stdClass')
    {
        $res = $this->query($sql);
        if (!$res || !($res instanceof \mysqli_result)) {
            return [];
        }
        $ret = [];
        while ($row = $res->fetch_object($class_name)) {
            $ret[] = $row;
        }
        $res->free();
        return $ret ? $ret : [];
    }

    /**
     * 返回影响行数
     *
     * @return int
     */
    public function affectedRows()
    {
        $this->connect();
        return $this->mysqli->affected_rows;
    }

    /**
     * 返回最新自增ID
     *
     * @return int
     */
    public function insertID()
    {
        $this->connect();
        return $this->mysqli->insert_id;
    }

    /**
     * 开启一个事务,只对InnoDB表起作用
     */
    public function startTransaction()
    {
        $this->connect();
        $this->mysqli->autocommit(false);
        $this->mysqli->begin_transaction();
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        $this->connect();
        $this->mysqli->commit();
        $this->mysqli->autocommit(true);
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->connect();
        $this->mysqli->rollback();
        $this->mysqli->autocommit(true);
    }

    /**
     * 获取mysql服务器版本信息
     *
     * @return string
     */
    public function getServerInfo()
    {
        $this->connect();
        return $this->mysqli->server_info;
    }

    /**
     * 获取当前查询返回记录数
     *
     * @return int
     */
    public function getNumRows()
    {
        return $this->result->num_rows;
    }

    /**
     * 写操作mysql数据库失败的日志
     *
     * @param int    $errno 错误编号
     * @param int    $error 错误信息
     * @param string $query 操作语句
     */
    private function writeErrLog($errno, $error, $query)
    {
        $e = new \mysqli_sql_exception();
        $arr = (array)$e->getTrace();
        $trace = (array)array_pop($arr);
        $err_file = (string)$trace['file'] . '(' . (string)$trace['line'] . ')';
        !IS_PRODUCTION && die($err_file . '=======' . $error . '=======' . $query);
        unset($e, $trace);
        $data = "file:{$err_file}" . PHP_EOL;
        $data .= "time:" . date('Y-m-d H:i:s') . PHP_EOL;
        $data .= "errno:{$errno}" . PHP_EOL;
        $data .= "error:{$error}" . PHP_EOL;
        $data .= "query:{$query}" . PHP_EOL;
        $data .= "======================================================================" . PHP_EOL;
        Log::writeErrLog('error_mysql' . date('Ymd'), $data, ConfigLog::ERR_MYSQL_LOG_TYPE);
        HelperReturn::jsonData('DB ERROR!', SException::CODE_MYSQL_ERROR);
    }

    /**
     * 关闭数据库连接，释放结果集内存
     */
    public function close()
    {
        if ($this->result instanceof \mysqli_result) {
            $this->result->free();
        }
        if ($this->mysqli instanceof \mysqli) {
            $this->mysqli->close();
        }
    }

}
