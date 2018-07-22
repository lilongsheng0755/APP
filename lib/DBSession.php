<?php

namespace lib;

/**
 * Author: skylong
 * CreateTime: 2018-7-7 20:41:32
 * Description: 基于MySQL数据库的一个session管理类
 */
class DBSession {

    /**
     * pdo对象
     *
     * @var \PDO 
     */
    protected static $pdo          = null;
    protected static $client_agent = null;
    protected static $client_ip    = null;
    protected static $life_time    = null;
    protected static $time         = null;

    public static function start(\PDO $pdo) {
        self::$pdo          = $pdo;
        self::$client_agent = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
        $client_ip          = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ?
                $_SERVER['HTTP_X_FORWARDED_FOR'] : (!empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0);

        filter_var($client_ip, FILTER_VALIDATE_IP) === false && $client_ip       = 0;
        self::$client_ip = $client_ip ? sprintf('%u', ip2long($client_ip)) : 0;
        self::$life_time = ini_get('session.gc_maxlifetime');
        self::$time      = time();

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
        $sql    = "SELECT * FROM session WHERE sid = ?";
        $sth    = self::$pdo->prepare($sql);
        $sth->execute(array($sid));
        if (!$result = $sth->fetch(PDO::FETCH_ASSOC)) {
            return '';
        }

        if (self::$client_ip != $result['client_ip'] || self::$client_agent != $result['user_agent']) {
            self::destroy($sid);
            return '';
        }

        if (($result['update_time'] + self::$life_time) < self::$time) {
            self::destroy($sid);
            return '';
        }

        return $result['data'];
    }

    public static function write($sid, $data) {
        $sql    = "SELECT * FROM session WHERE sid = ?";
        $sth    = self::$pdo->prepare($sql);
        $sth->execute(array($sid));
        if (!$result = $sth->fetch(PDO::FETCH_ASSOC)) {
            if ($result['data'] != $data || self::$time > ($result['update_time'] + 30)) {
                $sql = "UPDATE session SET update_time = ?, data = ? WHERE sid = ?";
                $sth = self::$pdo->prepare($sql);
                $sth->execute(array(self::$time, $data, $sid));
            }
        } else {
            if (!empty($data)) {
                $sql = "INSERT INTO session SET sid = ?, update_time = ?, client_ip = ?, user_agent = ?,data = ?";
                $sth = self::$pdo->prepare($sql);
                $sth->execute(array($sid, self::$time, self::$client_ip, self::$client_agent, $data));
            }
        }
        return true;
    }

    public static function destroy($sid) {
        $sql = "DELETE FROM session WHERE sid = ?";
        $sth = self::$pdo->prepare($sql);
        $sth->execute(array($sid));
        return true;
    }

    private static function gc($life_time) {
        $sql = "DELETE FROM session WHERE update_time < ?";
        $sth = self::$pdo->prepare($sql);
        $sth->execute(array(self::time - $life_time));
        return true;
    }

}
