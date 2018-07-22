<?php

namespace lib;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:16:27
 * Description: 基于memcached扩展的session管理类
 */
class MEMSession {

    const NS = 'session_';

    /**
     * memcached对象
     *
     * @var \Memcached
     */
    protected static $mem       = null;
    protected static $life_time = null;

    public static function start(\Memcached $mem) {
        self::$mem       = $mem;
        self::$life_time = ini_get('session.gc_maxlifetime');

        session_set_save_handler(
                array(__CLASS__, 'open'), array(__CLASS__, 'close'), array(__CLASS__, 'read'), array(__CLASS__, 'write'), array(__CLASS__, 'destroy'), array(__CLASS__, 'gc')
        );
        session_start();
    }

    private static function open($path, $name) {
        return true;
    }

    public static function close() {
        return true;
    }

    private static function read($sid) {
        $out = self::$mem->get(self::session_key($sid));
        if ($out === false || $out === null) {
            return $out;
        }
    }

    public static function write($sid, $data) {
        $method = $data ? 'set' : 'replace';
        return self::$mem->$method(self::session_key($sid), $data, self::$life_time);
    }

    public static function destroy($sid) {
        return self::$mem->delete(self::session_key($sid));
    }

    private static function gc($life_time) {
        return true;
    }

    private static function session_key($sid) {
        $session_key = '';
        if (defined('PROJECT_NS')) {
            $session_key .= PROJECT_NS;
        }
        $session_key .= self::NS . $sid;
        return $session_key;
    }

}
