<?php

namespace lib\db;

use lib\db\DataBase;

/**
 * Author: skylong
 * CreateTime: 2018-6-1 16:20:13
 * Description: 基于mysqli扩展的数据库操作类
 */
class Mysqli extends DataBase {

    /**
     * mysqli对象
     *
     * @var \mysqli
     */
    private $mysqli = null;

    /**
     * mysql实例初始化
     * 
     * @param string $host 数据库地址
     * @param string $username 数据库登录用户名
     * @param string $passwd  数据库登录密码
     * @param string $dbname 操作数据库名称
     * @param int $port  数据库端口
     */
    public function __construct($host, $username, $passwd, $dbname, $port = 3306) {
        class_exists('mysqli') or die('No mysqli extensions installed');
        $this->mysqli = new \mysqli($host, $username, $passwd, $dbname, $port);
        !$this->mysqli->connect_error or die('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        !$this->mysqli->set_charset("utf-8") or die("Error loading character set utf-8: {$this->mysqli->error}\n");
    }

    public function delete() {
        
    }

    public function insert() {
        
    }

    public function select() {
        
    }

    public function update() {
        
    }

    public function query($query) {
        if (!$flag = $this->mysqli->query($query)) {
            $err_file = 'test';
            $this->writeLog($err_file, $this->mysqli->errno, $this->mysqli->error, $query);
        }
        return $flag;
    }

    public function affectedRows() {
        return $this->mysqli->affected_rows;
    }

    public function startTransaction() {
        $this->mysqli->autocommit(false);
        $this->mysqli->begin_transaction();
    }

    public function commit() {
        $this->mysqli->commit();
        $this->mysqli->autocommit(true);
    }

    public function rollback() {
        $this->mysqli->rollback();
        $this->mysqli->autocommit(true);
    }

    private function __destruct() {
        $this->mysqli->close();
    }

}
