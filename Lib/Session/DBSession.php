<?php

namespace Lib\Session;

/**
 * Author: skylong
 * CreateTime: 2018-7-7 20:41:32
 * Description: 基于MySQL数据库的一个session管理类
 */
class DBSession {

    /**
     * pdo对象
     *
     * @var \Lib\DB\SPDO
     */
    protected static $db = null;

    /**
     * 保存session数据的表名
     *
     * @var string
     */
    protected static $tblname = null;

    /**
     * session表主键key为sessionID
     *
     * @var string
     */
    protected static $primary_key = null;

    /**
     * 客户端代理
     *
     * @var string
     */
    protected static $client_agent = null;

    /**
     * 客户端IP
     *
     * @var string
     */
    protected static $client_ip = null;

    /**
     * session存活时间
     *
     * @var int
     */
    protected static $life_time = null;

    /**
     * 当前时间戳
     *
     * @var int
     */
    protected static $time = null;

    /**
     * 自定义session初始化
     * 
     * @param object $db  \Lib\DB\SPDO | \Lib\DB\SMysqli
     * @param string $tblname  保存session数据的表名
     * @param string $primary_key  session表主键key  sessionID
     */
    public static function start($db, $tblname = 'user_session', $primary_key = 'sid') {
        ini_set('session.save_handler', 'user');
        ini_set('session.gc_maxlifetime', 1800);
        self::$db = $db;
        self::$client_agent = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
        $client_ip = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ?
                $_SERVER['HTTP_X_FORWARDED_FOR'] : (!empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0);

        filter_var($client_ip, FILTER_VALIDATE_IP) === false && $client_ip = '0.0.0.0';
        self::$client_ip = $client_ip;
        self::$life_time = ini_get('session.gc_maxlifetime');
        self::$time = time();
        self::$tblname = $tblname;
        self::$primary_key = $primary_key;

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
        $tblname = self::$tblname;
        $field = self::$primary_key;
        $sid = md5(trim($sid));
        $sql = "SELECT * FROM `{$tblname}` WHERE `{$field}`='{$sid}' LIMIT 1";
        if (!$result = self::$db->getOne($sql)) {
            return '';
        }

        //变更了IP或浏览器，需要销毁session数据
        if (self::$client_ip != $result['client_ip'] || self::$client_agent != $result['user_agent']) {
            self::destroy($sid);
            return '';
        }

        //如果时间过期了也要销毁session数据
        if (($result['update_time'] + self::$life_time) < self::$time) {
            self::destroy($sid);
            return '';
        }

        return $result['data'];
    }

    /**
     * 更新session操作
     * 
     * @param string $sid
     * @param string $data
     * @return boolean
     */
    public static function write($sid, $data) {
        $tblname = self::$tblname;
        $field = self::$primary_key;
        $update_time = self::$time;
        $client_ip = self::$client_ip;
        $user_agent = self::$client_agent;
        $sid = md5(trim($sid));
        $sql = "SELECT * FROM `{$tblname}` WHERE `{$field}`='{$sid}' LIMIT 1";
        if ($result = self::$db->getOne($sql)) {
            //数据有变动时更新，或者间隔30秒更新一次
            if ($result['data'] != $data || self::$time > ($result['update_time'] + 30)) {
                $sql = "UPDATE `{$tblname}` SET `update_time` = {$update_time}, `data` = '{$data}' WHERE `{$field}` = '{$sid}'";
                self::$db->query($sql);
            }
        } else {
            if (!empty($data)) {
                $sql = "INSERT INTO `{$tblname}` SET `{$field}` = '{$sid}', `update_time` = {$update_time}, `client_ip` = '{$client_ip}', `user_agent` = '{$user_agent}',`data` = '{$data}'";
                self::$db->query($sql);
            }
        }
        return true;
    }

    /**
     * 销毁session操作
     * 
     * @param string $sid
     * @return boolean
     */
    public static function destroy($sid) {
        $tblname = self::$tblname;
        $field = self::$primary_key;
        $sid = md5(trim($sid));
        $sql = "DELETE FROM `{$tblname}` WHERE `{$field}` = '{$sid}'";
        self::$db->query($sql);
        return true;
    }

    /**
     * 过期session数据垃圾回收（推荐用定时脚本清，多久清一次得看数据量）
     * 
     * @param string $life_time
     * @return boolean
     */
    public static function gc($life_time) {
        $tblname = self::$tblname;
        $update_time = self::$time - $life_time;
        $sql = "DELETE FROM `{$tblname}` WHERE `update_time` < $update_time";
        self::$db->query($sql);
        return true;
    }

}
