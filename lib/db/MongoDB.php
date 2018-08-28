<?php

namespace lib\db;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:22:42
 * Description: 
 */
class MongoDB {

    /**
     * mongodb对象
     *
     * @var  \MongoDB\Driver\Manager
     */
    private $mongo = null;

    /**
     * mongodb实例初始化
     * 
     * @param string $host 数据库地址
     * @param string $username 数据库登录用户名
     * @param string $passwd  数据库登录密码
     * @param string $dbname 操作数据库名称
     * @param int $port  数据库端口
     */
    public function __construct($host, $username, $passwd, $dbname, $port = 27017) {
        extension_loaded('mongodb') or die('No mongodb extensions installed');
        $dsn         = "mongodb://192.168.0.102:27018";
        $this->mongo = new \MongoDB\Driver\Manager('mongodb://192.168.0.102:27018');
    }

}
