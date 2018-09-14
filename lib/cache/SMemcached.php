<?php

namespace lib\cache;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:22:07
 * Description: memcached操作类
 */
class SMemcached {

    const KEY_EXPIRE = 2592000; //默认key有效时间为30天
    const TRY_NUM    = 2; //失败重连次数
    const KEY_PREFIX = ''; //设置key前缀

    private $memObj    = null; //memcached实例
    private $memServer = array(array('192.168.0.102', 11211, 100)); //memcached服务器组

    public function __construct($memServer = array()) {
        class_exists('memcached') or die('Non installed memcached extension!');
        ($memServer && is_array($memServer)) && $this->memServer = $memServer;
        $this->memObj    = new \Memcached();
        $this->memObj->setOption(\Memcached::OPT_BINARY_PROTOCOL, true); //开启使用二进制协议
        $this->memObj->setOption(\Memcached::OPT_TCP_NODELAY, true); //开启或关闭已连接socket的无延迟特性
        $this->memObj->setOption(\Memcached::OPT_NO_BLOCK, true); //开启或关闭异步I/O
        $this->memObj->setOption(\Memcached::OPT_DISTRIBUTION, \Memcached::DISTRIBUTION_CONSISTENT);  //一致性分布算法
        $this->memObj->setOption(\Memcached::OPT_PREFIX_KEY, self::KEY_PREFIX); //设置key的前缀
        $this->memObj->addServers($this->memServer);
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
            $this->memObj->add($key, $val, $expire);
            if ($this->memObj->getResultCode() === \Memcached::RES_SUCCESS) {
                return true;
            }
            if (in_array($this->memObj->getResultCode(), array(\Memcached::RES_BAD_KEY_PROVIDED, \Memcached::RES_NOTSTORED))) {
                return false;
            }
        }
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
            $this->memObj->set($key, $val, $expire);
            if ($this->memObj->getResultCode() === \Memcached::RES_SUCCESS) {
                return true;
            }
            if ($this->memObj->getResultCode() === \Memcached::RES_BAD_KEY_PROVIDED) {
                return false;
            }
        }
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
            $this->memObj->replace($key, $val, $expire);
            if ($this->memObj->getResultCode() === \Memcached::RES_SUCCESS) {
                return true;
            }
            if (in_array($this->memObj->getResultCode(), array(\Memcached::RES_NOTSTORED, \Memcached::RES_BAD_KEY_PROVIDED))) {
                return false;
            }
        }
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
        $expire = (int) $expire > 0 ? (int) $expire : self::KEY_EXPIRE;
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $res = $this->memObj->increment($key, $num);
            if ($res !== false && $this->memObj->getResultCode() === \Memcached::RES_SUCCESS) {
                return $res;
            }
            if (in_array($this->memObj->getResultCode(), array(\Memcached::RES_NOTFOUND, \Memcached::RES_BAD_KEY_PROVIDED))) {
                return false;
            }
        }
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
        $expire = (int) $expire > 0 ? (int) $expire : self::KEY_EXPIRE;
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $res = $this->memObj->decrement($key, $num);
            if ($res !== false && $this->memObj->getResultCode() === \Memcached::RES_SUCCESS) {
                return $res;
            }
            if (in_array($this->memObj->getResultCode(), array(\Memcached::RES_NOTFOUND, \Memcached::RES_BAD_KEY_PROVIDED))) {
                return false;
            }
        }
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
            $this->memObj->delete($key, $time);
            if ($this->memObj->getResultCode() === \Memcached::RES_SUCCESS) {
                return true;
            }
            if (in_array($this->memObj->getResultCode(), array(\Memcached::RES_NOTFOUND, \Memcached::RES_BAD_KEY_PROVIDED))) {
                return false;
            }
        }
        return false;
    }

    /**
     * 删除多个key
     * 
     * @param string $keys
     * @param int $time 等待多少秒后删除，默认0立即删除
     * @return boolean
     */
    public function delMulti($keys, $time = 0) {
        $time = (int) $time > 0 ? (int) $time : 0;
        for ($i = 0; $i < self::TRY_NUM; $i++) {
            $this->memObj->deleteMulti($keys, $time);
            if ($this->memObj->getResultCode() === \Memcached::RES_SUCCESS) {
                return true;
            }
            if (in_array($this->memObj->getResultCode(), array(\Memcached::RES_NOTFOUND, \Memcached::RES_BAD_KEY_PROVIDED))) {
                return false;
            }
        }
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
            $res = $this->memObj->get($key);
            if ($res) {
                return $res;
            }
            if (in_array($this->memObj->getResultCode(), array(\Memcached::RES_BAD_KEY_PROVIDED, \Memcached::RES_NOTFOUND))) {
                return false;
            }
        }
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
            $res = $this->memObj->getMulti($keys);
            if ($res) {
                return $res;
            }
            if (in_array($this->memObj->getResultCode() === \Memcached::RES_SOME_ERRORS)) {
                return false;
            }
        }
        return false;
    }

    /**
     * 删除所有缓存内容，但是不释放内存
     * 
     * @return bool
     */
    public function flush() {
        return $this->memObj->flush();
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
        return $this->memObj->touch($key, $expire);
    }

    /**
     * 获取memcached单个设置项
     * 
     * @param string $memOpt
     * @return int
     */
    public function getOption($memOpt) {
        return $this->memObj->getOption($memOpt);
    }

    /**
     * 获取memcached状态信息
     * 
     * @return bool|array
     */
    public function getStats() {
        return $this->memObj->getStats();
    }

    /**
     * 获取memcached版本
     * 
     * @return boolean
     */
    public function getVersion() {
        if (!$this->memObj) {
            return false;
        }
        return $this->memObj->getVersion();
    }

}
