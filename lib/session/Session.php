<?php

namespace lib\session;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:18:54
 * Description: 自定义session类，文件存在机制
 */
class Session {

    /**
     * session文件的保存路径
     *
     * @var string 
     */
    public static $session_save_path = null;

    /**
     * 执行session会话
     * 
     * @param string $session_save_path 保存session数据的路径
     */
    public static function start($session_save_path = '/tmp/session') {
        ini_set('session.save_handler', 'files');
        ini_set('session.gc_maxlifetime', 1800);
        $session_save_path && ini_set('session.save_path', $session_save_path);
        self::$session_save_path = rtrim($session_save_path ? $session_save_path : ini_get('session.save_path'), DS);
        session_set_save_handler(
                array(__CLASS__, 'open'), array(__CLASS__, 'close'), array(__CLASS__, 'read'), array(__CLASS__, 'write'), array(__CLASS__, 'destroy'), array(__CLASS__, 'gc')
        );
        session_start();
    }

    /**
     * 在运行session_start()时执行，该函数需要声明两个参数，系统会自动将php.ini中的session.save_path选项值传递给该函数的
     * 第一个参数，将session名自动传递给第二个参数中。返回true则可以继续往下执行。
     * 
     * @param string $save_path 
     * @param string $session_name
     * @return boolean
     */
    public static function open($save_path, $session_name) {
        unset($session_name);
        self::$session_save_path = $save_path;
        return true;
    }

    /**
     * 该函数需要参数，在脚本执行完成或者调用session_write_close()、session_destory()时被执行，即在所有session操作完成后
     * 被执行。如果不需要处理，则直接返回true即可。
     * 
     * @return boolean
     */
    public static function close() {
        return true;
    }

    /**
     * 在运行session_start()时执行，因为在开启回话时，会去read当前session数据并写入$_SESSION变量。需要声明一个参数，系统
     * 会自动将session ID传递给该参数，用于通过session ID获取对应的用户数据，返回当前用户的会话信息，写入$_SESSION变量。
     * 
     * @param string $session_id
     * @return string
     */
    public static function read($session_id) {
        $session_file = self::$session_save_path . DS . 'sess_' . md5($session_id);
        return (string) file_get_contents($session_file);
    }

    /**
     * 该函数在脚本结束和对$_SESSION变量赋值是执行。需要声明两个参数，分别是session ID 和串行化后的session信息字符串。
     * 在对$_SESSION变量赋值时，就可以通过session ID 找到存储的位置，并将信息写入。存储成功返回true继续往下执行。
     * 
     * @param string $session_id
     * @param string $session_data
     * @return boolean
     */
    public static function write($session_id, $session_data) {
        $session_file = self::$session_save_path . DS . 'sess_' . md5($session_id);
        if ($fp           = fopen($session_file, 'w')) {
            $ret = fwrite($fp, $session_data);
            fclose($fp);
            return (bool) $ret;
        } else {
            return false;
        }
    }

    /**
     * 在运行session_destory()时执行，需要声明一个参数，系统会自动将session ID 传递给该参数，去删除对应的session文件。
     * 
     * @param string $session_id
     * @return boolean
     */
    public static function destroy($session_id) {
        $session_file = self::$session_save_path . DS . 'sess_' . md5($session_id);
        return (bool) (unlink($session_file));
    }

    /**
     * 垃圾回收程序启动时执行。需要声明一个参数，系统会自动将php.ini中的session.gc_maxlifetime 选项的值传递给该参数，用于
     * 删除超过这个时间的session信息，返回true则可以继续往下执行。
     * 
     * @param int $maxlifetime
     * @return boolean
     */
    public static function gc($maxlifetime) {
        foreach (glob(self::$session_save_path . DS . 'sess_*') as $filename) {
            if (filemtime($filename) + $maxlifetime < time()) {
                (bool) (unlink($filename));
            }
        }
        return true;
    }

}
