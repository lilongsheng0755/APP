<?php

namespace lib\session;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:16:27
 * Description: 基于memcached扩展的session管理类
 */
class MEMSession {

    const NS = 'SESSION_';

    /**
     * memcached对象
     *
     * @var \lib\cache\SMemcached
     */
    protected static $mem = null;

    /**
     * session存活时间
     *
     * @var string
     */
    protected static $life_time = null;

    /**
     * 自定义session初始化
     * 
     * @param \lib\cache\SMemcached $mem
     */
    public static function start($mem) {
        self::$mem       = $mem;
        ini_set('session.save_handler', 'user');
        ini_set('session.gc_maxlifetime', 1800);
        self::$life_time = ini_get('session.gc_maxlifetime');
        session_set_save_handler(
                array(__CLASS__, 'open'), array(__CLASS__, 'close'), array(__CLASS__, 'read'), array(__CLASS__, 'write'), array(__CLASS__, 'destroy'), array(__CLASS__, 'gc')
        );
        session_start();
    }

    /**
     * session初始化时执行此操作
     * 
     * @param string $save_path php.ini文件中配置的session文件保存路径
     * @param type $session_name  session名称
     * @return boolean
     */
    public static function open($save_path, $session_name) {
        unset($save_path, $session_name);
        return true;
    }

    /**
     * 在脚本执行完成或者调用session_write_close()、session_destory()时被执行
     * @return boolean
     */
    public static function close() {
        return true;
    }

    /**
     * 读取session数据
     * 
     * @param type $sid  
     * @return string
     */
    public static function read($sid) {
        $out = self::$mem->get(self::session_key($sid));
        if ($out === false || $out === null) {
            return (string) $out;
        }
        return $out;
    }

    /**
     * 更新session操作
     * 
     * @param string $sid
     * @param string $data
     * @return boolean
     */
    public static function write($sid, $data) {
        $method = $data ? 'set' : 'replace';
        self::$mem->$method(self::session_key($sid), $data, self::$life_time);
        return true;
    }

    /**
     * 销毁session操作
     * 
     * @param string $sid
     * @return boolean
     */
    public static function destroy($sid) {
        return (bool) self::$mem->delete(self::session_key($sid));
    }

    /**
     * 过期session数据垃圾回收
     * 
     * @param string $life_time
     * @return boolean
     */
    public static function gc($life_time) {
        unset($life_time);
        return true;
    }

    /**
     * 自定义sessionKey
     * 
     * @param string $sid
     * @return string
     */
    private static function session_key($sid) {
        $session_key = '';
        $session_key .= self::NS . md5($sid);
        return strtoupper($session_key);
    }

}
