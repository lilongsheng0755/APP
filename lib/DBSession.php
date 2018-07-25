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
    protected static $pdo = null;

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
     * @param \PDO $pdo
     */
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

    /**
     * session初始化时执行此操作
     * 
     * @param string $save_path php.ini文件中配置的session文件保存路径
     * @param type $session_name  session名称
     * @return boolean
     */
    private static function open($save_path, $session_name) {
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
    private static function read($sid) {
        $sql    = "SELECT * FROM session WHERE sid = ?";
        $sth    = self::$pdo->prepare($sql);
        $sth->execute(array($sid));
        if (!$result = $sth->fetch(PDO::FETCH_ASSOC)) {
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
        $sql    = "SELECT * FROM session WHERE sid = ?";
        $sth    = self::$pdo->prepare($sql);
        $sth->execute(array($sid));
        if (!$result = $sth->fetch(PDO::FETCH_ASSOC)) {
            //数据有变动时更新，或者间隔30秒更新一次
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

    /**
     * 销毁session操作
     * 
     * @param string $sid
     * @return boolean
     */
    public static function destroy($sid) {
        $sql = "DELETE FROM session WHERE sid = ?";
        $sth = self::$pdo->prepare($sql);
        $sth->execute(array($sid));
        return true;
    }

    /**
     * 过期session数据垃圾回收
     * 
     * @param string $life_time
     * @return boolean
     */
    private static function gc($life_time) {
        $sql = "DELETE FROM session WHERE update_time < ?";
        $sth = self::$pdo->prepare($sql);
        $sth->execute(array(self::time - $life_time));
        return true;
    }

}
