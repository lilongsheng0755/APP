<?php

namespace Lib\DB;

use Lib\System\SException;
use Lib\System\Log;
use Config\ConfigLog;
use Helper\HelperReturn;
use \MongoDB\Driver\BulkWrite;
use \MongoDB\Driver\Exception\Exception;
use \MongoDB\Driver\Manager;
use \MongoDB\Driver\WriteConcern;
use \MongoDB\Driver\Query;
use \MongoDB\Driver\ReadPreference;
use \MongoDB\BSON\ObjectID;
use \MongoDB\Driver\Exception\BulkWriteException;
use \MongoDB\Driver\Exception\InvalidArgumentException;
use \MongoDB\Driver\Exception\ConnectionException;
use \MongoDB\Driver\Exception\AuthenticationException;
use \MongoDB\Driver\Exception\RuntimeException;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:22:42
 * Description: 基于MongoDB扩展的操作管理类(PHP7+)
 */
class MongoDB
{

    /**
     * 操作的数据库名
     *
     * @var string
     */
    private $dbname = null;

    /**
     * ManGoDB 驱动程序管理器
     *
     * @var \MongoDB\Driver\Manager
     */
    private $manager = null;

    /**
     * mongodb实例初始化
     *
     * @param string $host     数据库地址
     * @param string $username 数据库登录用户名
     * @param string $passwd   数据库登录密码
     * @param string $dbname   操作数据库名称
     * @param int    $port     数据库端口
     */
    public function __construct($host, $username, $passwd, $dbname, $port = 27017)
    {
        extension_loaded('mongodb') or die('No mongodb extensions installed');
        $this->dbname = $dbname;
        $dsn = "mongodb://{$username}:{$passwd}@{$host}:{$port}/{$dbname}";
        try {
            $this->manager = new Manager($dsn);
        } catch (InvalidArgumentException $e) {
            $this->initManagerErrLog($e);
        } catch (RuntimeException $e) {
            $this->initManagerErrLog($e);
        }
    }

    /**
     * MongoDB 初始化异常写错误日志
     *
     * @param \Exception $e
     */
    private function initManagerErrLog($e)
    {
        $arr = (array)$e->getTrace();
        $trace = (array)array_pop($arr);
        $err_file = (string)$trace['file'] . '(' . (string)$trace['line'] . ')';
        $this->writeErrLog($err_file, $e->getCode(), $e->getMessage(), '<= - =>');
    }

    /**
     * 插入一条数据
     *
     * @param string $table_name 集合名（表名）
     * @param array  $data       需要写入的数据
     *
     * @return boolean|int 成功返回影响行数，失败返回false
     */
    public function insert($table_name = 'test', $data = [])
    {
        try {
            $bulk = new BulkWrite();
            $bulk->insert($data);
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 100);
            $result = $this->manager->executeBulkWrite($this->dbname . '.' . $table_name, $bulk, $writeConcern);
            if ($result->getWriteErrors()) {
                return false;
            }
            return (int)$result->getInsertedCount();
        } catch (BulkWriteException $e) {
            $this->insertErrLog($e, $data);
        } catch (InvalidArgumentException $e) {
            $this->insertErrLog($e, $data);
        } catch (AuthenticationException $e) {
            $this->insertErrLog($e, $data);
        } catch (ConnectionException $e) {
            $this->insertErrLog($e, $data);
        } catch (RuntimeException $e) {
            $this->insertErrLog($e, $data);
        }
        return true;
    }

    /**
     * 写入发生异常写错误日志
     *
     * @param \Exception $e
     * @param array      $insert_data
     */
    private function insertErrLog($e, $insert_data = [])
    {
        $arr = (array)$e->getTrace();
        $trace = (array)array_pop($arr);
        $err_file = (string)$trace['file'] . '(' . (string)$trace['line'] . ')';
        $this->writeErrLog($err_file, $e->getCode(), $e->getMessage(), 'insert--' . json_encode($insert_data));
    }

    /**
     * 查询数据
     *
     * @param string $table_name 集合名（表名）
     * @param array  $filter     过滤条件：[字段名=>['$lt'=>10]],mongodb分别使用$lt、$lte、$eq、$gte、$gt、$ne表示小于、小于等于、等于、大于等于、大于、不等于，用于整数字段查询
     * @param array  $options    操作项：['sort'=>['views'=>-1]],以视图的降序返回文档
     *
     * @return array
     */
    public function query($table_name, $filter = [], $options = [])
    {
        try {
            $query = new Query($filter, $options);
            /**
             * MongoDB\Driver\ReadPreference::RP_PRIMARY 当前主库中读取
             * MongoDB\Driver\ReadPreference::RP_PRIMARY_PREFERRED  当前库中读取，如果不可用则从次要库读取
             * MongoDB\Driver\ReadPreference::RP_SECONDARY 从库中读取
             * MongoDB\Driver\ReadPreference::RP_SECONDARY_PREFERRED 从库中读取，如果从库不可用，则从主库中读取。
             * MongoDB\Driver\ReadPreference::RP_NEAREST 从最少网络延迟的从库读取
             */
            $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);
            $result = $this->manager->executeQuery($this->dbname . '.' . $table_name, $query, $readPreference)->toArray();
            if (!$result || !is_array($result)) {
                return [];
            }
            return $this->objectToArray($result);
        } catch (InvalidArgumentException $e) {
            $this->queryErrLog($e, $filter, $options);
        } catch (AuthenticationException $e) {
            $this->queryErrLog($e, $filter, $options);
        } catch (ConnectionException $e) {
            $this->queryErrLog($e, $filter, $options);
        } catch (RuntimeException $e) {
            $this->queryErrLog($e, $filter, $options);
        } catch (Exception $e) {
            $this->queryErrLog($e, $filter, $options);
        }

        return [];
    }

    /**
     * 查询发生异常写错误日志
     *
     * @param \Exception $e
     * @param array      $filter
     * @param array      $options
     */
    private function queryErrLog($e, $filter = [], $options = [])
    {
        $arr = (array)$e->getTrace();
        $trace = (array)array_pop($arr);
        $err_file = (string)$trace['file'] . '(' . (string)$trace['line'] . ')';
        $this->writeErrLog($err_file, $e->getCode(), $e->getMessage(), 'query--' . json_encode($filter) . '|--|' . json_encode($options));
    }

    /**
     * 删除操作
     *
     * @param string $table_name 集合名（表名）
     * @param array  $filter     操作条件
     * @param bool   $limit      false-删除匹配的所有文档，true-删除匹配的第一个文档
     *
     * @return boolean|int 成功返回影响行数，失败返回false
     */
    public function delete($table_name, $filter = [], $limit = true)
    {
        try {
            $bulk = new BulkWrite();
            $bulk->delete($filter, ['limit' => $limit]);
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 100);
            $result = $this->manager->executeBulkWrite($this->dbname . '.' . $table_name, $bulk, $writeConcern);
            if ($result->getWriteErrors()) {
                return false;
            }
            return (int)$result->getDeletedCount();
        } catch (BulkWriteException $e) {
            $this->deleteErrLog($e, $filter);
        } catch (InvalidArgumentException $e) {
            $this->deleteErrLog($e, $filter);
        } catch (AuthenticationException $e) {
            $this->deleteErrLog($e, $filter);
        } catch (ConnectionException $e) {
            $this->deleteErrLog($e, $filter);
        } catch (RuntimeException $e) {
            $this->deleteErrLog($e, $filter);
        }
        return true;
    }

    /**
     * 删除发生异常写错误日志
     *
     * @param \Exception $e
     * @param array      $filter
     */
    private function deleteErrLog($e, $filter = [])
    {
        $arr = (array)$e->getTrace();
        $trace = (array)array_pop($arr);
        $err_file = (string)$trace['file'] . '(' . (string)$trace['line'] . ')';
        $this->writeErrLog($err_file, $e->getCode(), $e->getMessage(), 'delete--' . json_encode($filter));
    }

    /**
     * 更新操作
     *
     * @param string  $table_name 集合名（表名）
     * @param array   $filter     需要更新的数据
     * @param array   $data       更新的条件
     * @param boolean $multi      false：只更新第一个匹配文档；true：更新所有匹配文档。
     * @param boolean $upsert     false：如果没有匹配的文档则不更新数据；true：如果没有匹配文档则写入一条新的记录
     *
     * @return boolean|int 成功返回影响行数，失败返回false
     */
    public function update($table_name = 'test', $filter = [], $data = [], $multi = false, $upsert = true)
    {
        try {
            $bulk = new BulkWrite();
            $bulk->update($filter, ['$set' => $data], ['multi' => $multi, 'upsert' => $upsert]);
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 100);
            $result = $this->manager->executeBulkWrite($this->dbname . '.' . $table_name, $bulk, $writeConcern);
            if ($result->getWriteErrors()) {
                return false;
            }
            return (int)$result->getModifiedCount();
        } catch (BulkWriteException $e) {
            $this->updateErrLog($e, $filter, $data);
        } catch (InvalidArgumentException $e) {
            $this->updateErrLog($e, $filter, $data);
        } catch (AuthenticationException $e) {
            $this->updateErrLog($e, $filter, $data);
        } catch (ConnectionException $e) {
            $this->updateErrLog($e, $filter, $data);
        } catch (RuntimeException $e) {
            $this->updateErrLog($e, $filter, $data);
        }
        return true;
    }

    /**
     * 更新发生异常写错误日志
     *
     * @param \Exception $e
     * @param array      $filter
     * @param array      $update_data
     */
    private function updateErrLog($e, $filter = [], $update_data = [])
    {
        $arr = (array)$e->getTrace();
        $trace = (array)array_pop($arr);
        $err_file = (string)$trace['file'] . '(' . (string)$trace['line'] . ')';
        $this->writeErrLog($err_file, $e->getCode(), $e->getMessage(), 'update--' . json_encode($filter) . '|--|' . json_encode($update_data));
    }

    /**
     * 获取\MongoDB\BSON\ObjectID对象
     *
     * @param string $id
     *
     * @return ObjectID
     */
    public function getObjectID($id = '')
    {
        if ($id && strlen(trim($id)) == 24) {
            $id = new ObjectID($id);
        } else {
            $id = new ObjectID();
        }
        return $id;
    }

    /**
     * 对象转换为数组
     *
     * @param array $data
     *
     * @return array
     */
    private function objectToArray($data)
    {
        if (!$data || !is_array($data)) {
            return [];
        }
        $ret = [];
        foreach ($data as $key => $val) {
            $temp = json_encode($val);
            $temp = json_decode($temp, true);
            if (isset($temp['_id']) && is_array($temp['_id'])) {
                $temp['oid'] = array_pop($temp['_id']);
                unset($temp['_id']);
            }
            $ret[$key] = $temp;
        }
        return $ret;
    }

    /**
     * 写操作mongodb数据库失败的日志
     *
     * @param int    $err_file 发生错误的位置
     * @param int    $errno    错误编号
     * @param int    $error    错误信息
     * @param string $query    操作语句
     */
    private function writeErrLog($err_file, $errno, $error, $query)
    {
        !IS_PRODUCTION && die($err_file . '=======' . $error . '=======' . $query);
        $data = "file:{$err_file}" . PHP_EOL;
        $data .= "time:" . date('Y-m-d H:i:s') . PHP_EOL;
        $data .= "errno:{$errno}" . PHP_EOL;
        $data .= "error:{$error}" . PHP_EOL;
        $data .= "cmd:{$query}" . PHP_EOL;
        $data .= "======================================================================" . PHP_EOL;
        Log::writeErrLog('error_mongodb' . date('Ymd'), $data, ConfigLog::ERR_MONGODB_LOG_TYPE);
        HelperReturn::jsonData('mongodb ERROR!', SException::CODE_MONGODB_ERROR);
    }

}
