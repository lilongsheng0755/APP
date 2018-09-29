<?php

namespace Lib\Cache;

defined('IN_APP') or die('Access denied!');

use Lib\Log;
use Config\ConfigLog;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:22:18
 * Description: 基于php redis扩展的操作管理类
 */
class SRedis {

    /**
     * 访问redis地址
     *
     * @var string
     */
    private $host = '';

    /**
     * 访问redis端口号
     *
     * @var int
     */
    private $port = 6379;

    /**
     * 访问redis需要的密码
     *
     * @var string
     */
    private $passwd = '';

    /**
     * redis是否连接成功
     *
     * @var boolean
     */
    private $is_connect = false;

    /**
     * redis请求超时秒数
     */
    const TIME_OUT = 3;

    /**
     * redis实例
     *
     * @var \Redis 
     */
    private $redis = null;

    /**
     * 参数初始化
     * 
     * @param string $host
     * @param int $port
     * @param string $passwd
     */
    public function __construct($host, $port = 6379, $passwd = '') {
        $this->host   = $host;
        $this->port   = $port;
        $this->passwd = $passwd;
    }

    /**
     * 创建redis连接
     * 
     * @return boolean
     */
    private function connect() {
        if ($this->is_connect) {
            return $this->is_connect;
        }
        try {
            $this->redis      = new \Redis();
            $this->is_connect = $this->redis->connect($this->host, $this->port, self::TIME_OUT);
            $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE); //Redis::SERIALIZER_NONE不序列化.Redis::SERIALIZER_IGBINARY二进制序列化
            $this->passwd && $this->redis->auth($this->passwd);
            defined('PROJECT_NS') && $this->redis->setOption(\Redis::OPT_PREFIX, strtoupper(PROJECT_NS . '_')); //设置key的前缀
        } catch (\RedisException $e) {
            $arr      = (array) $e->getTrace();
            $trace    = (array) array_pop($arr);
            $err_file = (string) $trace['file'] . '(' . (string) $trace['line'] . ')';
            $this->writeErrLog($err_file, $e->getCode(), $e->getMessage());
        }
        return $this->is_connect;
    }

    /**
     * String数据类型  获取值
     * 
     * @param string $key
     * @return string|boolean
     */
    public function get($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->get($key);
    }

    /**
     * String数据类型  设置值
     * 
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function set($key, $value) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->set($key, $value);
    }

    /**
     * String数据类型 设置一个有生命周期的值
     * 
     * @param string $key
     * @param string $value
     * @param int $expire  失效时间秒数
     * @return boolean
     */
    public function setex($key, $value, $expire = 86400) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->setex($key, $value, (int) $expire);
    }

    /**
     * String数据类型  设置值
     * 这个函数会先判断Redis中是否有这个KEY，如果没有就SET，有就返回False
     * 
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function setnx($key, $value) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->setnx($key, $value);
    }

    /**
     * String数据类型  添加字符串到指定KEY的字符串中
     * 
     * @param string $key
     * @param string $value
     * @return boolean|int 成功返回追加后的字符串长度
     */
    public function append($key, $value) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->append($key, $value);
    }
    

    /**
     * String数据类型 对指定KEY的值自增
     * 
     * @param string $key
     * @param int|float $num
     * @return boolean|int|float  返回自增后的值
     */
    public function incr($key, $num = 1) {
        if (!$this->connect()) {
            return false;
        }
        if (is_int($num) && $num > 1) {
            return $this->redis->incrBy($key, $num);
        }
        if (is_float($num)) {
            return $this->redis->incrByFloat($key, $num);
        }
        return $this->redis->incr($key);
    }

    /**
     * String数据类型 对指定KEY的值自减
     * 
     * @param string $key
     * @param int $num
     * @return boolean|int  返回自减后的值
     */
    public function decr($key, $num = 1) {
        if (!$this->connect()) {
            return false;
        }
        if (is_int($num) && $num > 1) {
            return $this->redis->decrBy($key, $num);
        }
        return $this->redis->decr($key);
    }

    /**
     * 删除key
     * 
     * @param string|array $keys
     * @return int 成功删除的个数
     */
    public function delete($keys) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->delete($keys);
    }

    /**
     * 验证一个指定的KEY是否存在
     * 
     * @param type $key
     * @return boolean
     */
    public function exists($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->exists($key);
    }

    /**
     * 写操作redis失败的日志
     * 
     * @param string $err_file  发生错误文件
     * @param int $errno  错误编号
     * @param int $error  错误信息
     */
    private function writeErrLog($err_file, $errno, $error) {
        APP_DEBUG && die($err_file . '=======' . $error);
        $data = "file:{$err_file}\r\n";
        $data .= "time:" . date('Y-m-d H:i:s') . "\r\n";
        $data .= "errno:{$errno}\r\n";
        $data .= "error:\"{$error}\"\r\n";
        $data .= "======================================================================\r\n";
        Log::writeErrLog('error_redis' . date('Ymd'), $data, ConfigLog::REDIS_EER_LOG_TYPE);
    }

}
