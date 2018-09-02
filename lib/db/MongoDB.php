<?php

namespace lib\db;

use lib\SException;
use lib\Log;
use lib\HelperReturn;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:22:42
 * Description: 
 */
class MongoDB {

    /**
     * mongodb对象
     *
     * @var string
     */
    private $host = null;

    /**
     * 用户名
     *
     * @var string
     */
    private $username = null;

    /**
     * 密码
     *
     * @var string
     */
    private $passwd = null;

    /**
     * 操作的数据库名
     *
     * @var string
     */
    private $dbname = null;

    /**
     * 访问数据库端口
     *
     * @var string 
     */
    private $port = null;

    /**
     * ManGoDB 驱动程序管理器
     *
     * @var MongoDB\Driver\Manager  
     */
    private $manager = null;

    /**
     * mongodb实例初始化
     * 
     * @param string $host 数据库地址
     * @param string $username 数据库登录用户名
     * @param string $passwd  数据库登录密码
     * @param string $dbname 操作数据库名称
     * @param int $port  数据库端口
     */
    public function __construct($host, $username, $passwd, $dbname, $port = 27017) {
        extension_loaded('mongodb') or die('No mongodb extensions installed');
        try {
            $this->host     = $host;
            $this->username = $username;
            $this->passwd   = $passwd;
            $this->dbname   = $dbname;
            $this->port     = $port;
            $dsn            = "mongodb://{$username}:{$passwd}@{$host}:{$port}/{$dbname}";
            $this->manager  = new \MongoDB\Driver\Manager($dsn);
        } catch (Exception $e) {
            if (APP_DEBUG) {
                die('Connection failed: ' . $e->getMessage());
            } else {
                die('Connection failed!');
            }
        }
    }

    /**
     * 插入一条数据
     * 
     * @param string $table_name  集合名（表名）
     * @param array $data 数据
     * @return boolean 成功返回true，失败返回false
     */
    public function insert($table_name = 'test', $data = array()) {
        try {
            $bulk         = new \MongoDB\Driver\BulkWrite();
            $bulk->insert($data);
            $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 100);
            $result       = $this->manager->executeBulkWrite($this->dbname . '.' . $table_name, $bulk, $writeConcern);
            if ($result->getWriteErrors()) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            $trace    = (array) array_pop($e->getTrace());
            $err_file = (string) $trace['file'] . '(' . (string) $trace['line'] . ')';
            $this->writeErrLog($err_file, $e->getCode(), $e->getMessage(), json_encode($data));
        }
    }

    public function query($table_name, $option = array()) {
        try {
            $bulk         = new \MongoDB\Driver\BulkWrite();
            $bulk->update($option, ['$set' => $data], ['multi' => $multi, 'upsert' => $upsert]);
            $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 100);
            $result       = $this->manager->executeBulkWrite($this->dbname . '.' . $table_name, $bulk, $writeConcern);
            if ($result->getWriteErrors()) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            $trace    = (array) array_pop($e->getTrace());
            $err_file = (string) $trace['file'] . '(' . (string) $trace['line'] . ')';
            $this->writeErrLog($err_file, $e->getCode(), $e->getMessage(), json_encode($data));
        }
    }

    /**
     * 删除操作
     * 
     * @param type $table_name  集合名（表名）
     * @param type $option 操作条件
     * @param type $limit false-删除匹配的所有文档，true-删除匹配的第一个文档
     * @return boolean
     */
    public function delete($table_name, $option = array(), $limit = true) {
        try {
            $bulk         = new \MongoDB\Driver\BulkWrite();
            $bulk->delete($option, ['limit' => $limit]);
            $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 100);
            $result       = $this->manager->executeBulkWrite($this->dbname . '.' . $table_name, $bulk, $writeConcern);
            if ($result->getWriteErrors()) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            $trace    = (array) array_pop($e->getTrace());
            $err_file = (string) $trace['file'] . '(' . (string) $trace['line'] . ')';
            $this->writeErrLog($err_file, $e->getCode(), $e->getMessage(), json_encode($option));
        }
    }

    /**
     * 更新操作
     * 
     * @param string $table_name  集合名（表名）
     * @param array $option  需要更新的数据
     * @param array $data  更新的条件
     * @param boolean $multi false：只更新第一个匹配文档；true：更新所有匹配文档。
     * @param boolean $upsert  false：如果没有匹配的文档则不更新数据；true：如果没有匹配文档则写入一条新的记录
     * @return boolean
     */
    public function update($table_name = 'test', $option = array(), $data = array(), $multi = false, $upsert = false) {
        try {
            $bulk         = new \MongoDB\Driver\BulkWrite();
            $bulk->update($option, ['$set' => $data], ['multi' => $multi, 'upsert' => $upsert]);
            $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 100);
            $result       = $this->manager->executeBulkWrite($this->dbname . '.' . $table_name, $bulk, $writeConcern);
            if ($result->getWriteErrors()) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            $trace    = (array) array_pop($e->getTrace());
            $err_file = (string) $trace['file'] . '(' . (string) $trace['line'] . ')';
            $this->writeErrLog($err_file, $e->getCode(), $e->getMessage(), json_encode($option));
        }
    }

    /**
     * 写操作mysql数据库失败的日志
     * 
     * @param int $err_file  发生错误的位置
     * @param int $errno  错误编号
     * @param int $error  错误信息
     * @param string $query  操作语句
     */
    private function writeErrLog($err_file, $errno, $error, $query) {
        APP_DEBUG && die($err_file . '=======' . $error . '=======' . $query);
        $data = "file:{$err_file}\r\n";
        $data .= "time:" . date('Y-m-d H:i:s') . "\r\n";
        $data .= "errno:{$errno}\r\n";
        $data .= "error:\"{$error}\"\r\n";
        $data .= "query:\"{$query}\"\r\n";
        $data .= "======================================================================\r\n";
        Log::writeErrLog('error_mongodb' . date('Ymd'), $data);
        HelperReturn::jsonData('mongodb ERROR!', SException::CODE_MONGODB_ERROR);
    }

}
