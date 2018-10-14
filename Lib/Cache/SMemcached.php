<?php

namespace Lib\Cache;

defined('IN_APP') or die('Access denied!');

use Lib\System\Log;
use Config\ConfigLog;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:22:07
 * Description: 基于php memcached扩展操作类
 */
class SMemcached {

    const KEY_EXPIRE = 2592000; //默认key有效时间为30天
    const TRY_NUM    = 2; //失败重连次数

    /**
     * memcached实例
     *
     * @var \memcached
     */
    private $mem = null;

    /**
     * memcached服务器组
     *
     * @var array 
     */
    private $mem_conf = array(array('192.168.0.102', 11211, 100));

    /**
     * memcached服务器组 例如：array(array('192.168.0.102', 11211, 100));
     * 
     * @param array $mem_conf
     */
    public function __construct($mem_conf = array()) {
        class_exists('memcached') or die('Non installed memcached extension!');
        ($mem_conf && is_array($mem_conf)) && $this->mem_conf = $mem_conf;
        $this->mem      = new \Memcached();
        $this->mem->setOption(\Memcached::OPT_BINARY_PROTOCOL, true); //开启使用二进制协议
        $this->mem->setOption(\Memcached::OPT_TCP_NODELAY, true); //开启或关闭已连接socket的无延迟特性
        $this->mem->setOption(\Memcached::OPT_NO_BLOCK, true); //开启或关闭异步I/O
        $this->mem->setOption(\Memcached::OPT_DISTRIBUTION, \Memcached::DISTRIBUTION_CONSISTENT);  //一致性分布算法
        defined('PROJECT_NS') && $this->mem->setOption(\Memcached::OPT_PREFIX_KEY, strtoupper(PROJECT_NS . '_')); //设置key的前缀
        $this->mem->addServers($this->mem_conf);
    }

    /**
     * 添加数据到memcached
     * 
     * @param string $key 存储key
     * @param string|array $val  存储内容
     * @param int $expire  过期时间（秒），默认30天有效
     * @return boolean 成功返回true，失败返回false
     */
    public function add($key, $val, $expire = 0) {
        $expire = (int) $expire > 0 ? (int) $expire : self::KEY_EXPIRE;
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $this->mem->add($key, $val, $expire);
            $resultCode = $this->mem->getResultCode();
            if ($resultCode === \Memcached::RES_SUCCESS) {
                return true;
            }
            if (in_array($resultCode, array(\Memcached::RES_BAD_KEY_PROVIDED, \Memcached::RES_NOTSTORED))) {
                return false;
            }
        }
        $this->writeErrLog($resultCode, $this->mem->getResultMessage(), "add->{$key}->$val");
        return false;
    }

    /**
     * set数据到memcached
     * 
     * @param string $key 存储key
     * @param string|array $val  存储内容
     * @param int $expire  过期时间（秒），默认30天有效
     * @return boolean 成功返回true，失败返回false
     */
    public function set($key, $val, $expire = 0) {
        $expire = (int) $expire > 0 ? (int) $expire : self::KEY_EXPIRE;
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $this->mem->set($key, $val, $expire);
            $resultCode = $this->mem->getResultCode();
            if ($resultCode === \Memcached::RES_SUCCESS) {
                return true;
            }
            if ($resultCode === \Memcached::RES_BAD_KEY_PROVIDED) {
                return false;
            }
        }
        $this->writeErrLog($resultCode, $this->mem->getResultMessage(), "set->{$key}->$val");
        return false;
    }

    /**
     * 替换数据，仅当key存在的时候替换，否则替换失败
     * 
     * @param string $key 存储key
     * @param string|array $val  存储内容
     * @param int $expire  过期时间（秒），默认30天有效
     * @return boolean 成功返回true，失败返回false
     */
    public function replace($key, $val, $expire = 0) {
        $expire = (int) $expire > 0 ? (int) $expire : self::KEY_EXPIRE;
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $this->mem->replace($key, $val, $expire);
            $resultCode = $this->mem->getResultCode();
            if ($resultCode === \Memcached::RES_SUCCESS) {
                return true;
            }
            if (in_array($resultCode, array(\Memcached::RES_NOTSTORED, \Memcached::RES_BAD_KEY_PROVIDED))) {
                return false;
            }
        }
        $this->writeErrLog($resultCode, $this->mem->getResultMessage(), "replace->{$key}->$val");
        return false;
    }

    /**
     * 增加数值元素的值
     * 
     * @param string $key
     * @param int $num   每次增加多少
     * @return boolean|int
     */
    public function incr($key, $num = 1) {
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $res        = $this->mem->increment($key, $num);
            $resultCode = $this->mem->getResultCode();
            if ($res !== false && $resultCode === \Memcached::RES_SUCCESS) {
                return $res;
            }
            if (in_array($resultCode, array(\Memcached::RES_NOTFOUND, \Memcached::RES_BAD_KEY_PROVIDED))) {
                return false;
            }
        }
        $this->writeErrLog($resultCode, $this->mem->getResultMessage(), "incr->{$key}->$num");
        return false;
    }

    /**
     * 减小数值元素的值
     * 
     * @param string $key
     * @param int $num   每次减少多少
     * @return boolean|int
     */
    public function decr($key, $num = 1) {
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $res        = $this->mem->decrement($key, $num);
            $resultCode = $this->mem->getResultCode();
            if ($res !== false && $resultCode === \Memcached::RES_SUCCESS) {
                return $res;
            }
            if (in_array($resultCode, array(\Memcached::RES_NOTFOUND, \Memcached::RES_BAD_KEY_PROVIDED))) {
                return false;
            }
        }
        $this->writeErrLog($resultCode, $this->mem->getResultMessage(), "decr->{$key}->$num");
        return false;
    }

    /**
     * 删除一个key
     * 
     * @param string $key
     * @param int $time  等待多少秒后删除，默认0立即删除
     * @return boolean
     */
    public function delete($key, $time = 0) {
        $time = (int) $time > 0 ? (int) $time : 0;
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $this->mem->delete($key, $time);
            $resultCode = $this->mem->getResultCode();
            if ($resultCode === \Memcached::RES_SUCCESS) {
                return true;
            }
            if (in_array($resultCode, array(\Memcached::RES_NOTFOUND, \Memcached::RES_BAD_KEY_PROVIDED))) {
                return false;
            }
        }
        $this->writeErrLog($resultCode, $this->mem->getResultMessage(), "delete->{$key}");
        return false;
    }

    /**
     * 删除多个key
     * 
     * @param array $keys
     * @param int $time 等待多少秒后删除，默认0立即删除
     * @return boolean
     */
    public function delMulti($keys, $time = 0) {
        $time = (int) $time > 0 ? (int) $time : 0;
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $this->mem->deleteMulti($keys, $time);
            $resultCode = $this->mem->getResultCode();
            if ($resultCode === \Memcached::RES_SUCCESS) {
                return true;
            }
            if (in_array($resultCode, array(\Memcached::RES_NOTFOUND, \Memcached::RES_BAD_KEY_PROVIDED))) {
                return false;
            }
        }
        $key = implode(',', $keys);
        $this->writeErrLog($resultCode, $this->mem->getResultMessage(), "delMulti->{$key}");
        return false;
    }

    /**
     * 从memcached中获取一个值
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $res        = $this->mem->get($key);
            $resultCode = $this->mem->getResultCode();
            if ($resultCode === \Memcached::RES_SUCCESS) {
                return $res;
            }
            if (in_array($resultCode, array(\Memcached::RES_BAD_KEY_PROVIDED, \Memcached::RES_NOTFOUND))) {
                return false;
            }
        }
        $this->writeErrLog($resultCode, $this->mem->getResultMessage(), "get->{$key}");
        return false;
    }

    /**
     * 从memcached中获取多个值
     * 
     * @param array $keys
     * @return mixed
     */
    public function getMulti($keys) {
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $res        = $this->mem->getMulti($keys);
            $resultCode = $this->mem->getResultCode();
            if ($res) {
                return $res;
            }
            if (in_array($resultCode === \Memcached::RES_SOME_ERRORS)) {
                return false;
            }
        }
        $key = implode(',', $keys);
        $this->writeErrLog($resultCode, $this->mem->getResultMessage(), "getMulti->{$key}");
        return false;
    }

    /**
     * 删除所有缓存内容，但是不释放内存
     * 
     * @return bool
     */
    public function flush() {
        return $this->mem->flush();
    }

    /**
     * 重新给key设置过期时间
     * 
     * @param string $key
     * @param int $expire
     * @return bool
     */
    public function touch($key, $expire = 86400) {
        $expire = (int) $expire > 0 ? (int) $expire : self::KEY_EXPIRE;
        return $this->mem->touch($key, $expire);
    }

    /**
     * 获取memcached单个设置项
     * 
     * @param string $memOpt
     * @return int
     */
    public function getOption($memOpt) {
        return $this->mem->getOption($memOpt);
    }

    /**
     * 获取memcached状态信息
     * 
     * @return bool|array
     */
    public function getStats() {
        return $this->mem->getStats();
    }

    /**
     * 获取memcached版本
     * 
     * @return boolean
     */
    public function getVersion() {
        if (!$this->mem) {
            return false;
        }
        return $this->mem->getVersion();
    }

    /**
     * 写操作memcache失败的日志
     * 
     * @param int $errno  错误编号
     * @param int $error  错误信息
     * @param string $cmd  操作指令
     */
    private function writeErrLog($errno, $error, $cmd) {
        $e        = new \Exception();
        $arr      = (array) $e->getTrace();
        $trace    = (array) array_pop($arr);
        $err_file = (string) $trace['file'] . '(' . (string) $trace['line'] . ')';
        !PRODUCTION_ENV && die($err_file . '=======' . $error . '=======' . $cmd);
        $data     = "file:{$err_file}\r\n";
        $data     .= "time:" . date('Y-m-d H:i:s') . "\r\n";
        $data     .= "errno:{$errno}\r\n";
        $data     .= "error:\"{$error}\"\r\n";
        $data     .= "cmd:\"{$cmd}\"\r\n";
        $data     .= "======================================================================\r\n";
        Log::writeErrLog('error_memcached' . date('Ymd'), $data, ConfigLog::MEM_ERR_LOG_TYPE);
    }

}
