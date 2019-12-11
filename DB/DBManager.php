<?php
/**
 * Author: skylong
 * CreateTime: 2019/12/11 13:46
 * Description: 数据库管理模型
 */

namespace DB;


class DBManager
{

    /**
     * ManagerMySql实例
     *
     * @var DBManager
     */
    private static $instance = null;

    /**
     * mysqli实例
     *
     * @var \mysqli
     */
    private $mysqli = null;

    /**
     * 数据库差异【保存不存在的库名】
     *
     * @var array
     */
    private $diff_db = [];

    /**
     * 数据表差异【保存不存在的表名】
     *
     * @var array
     */
    private $diff_table = [];

    /**
     * 字段属性差异
     *
     * @var array
     */
    private $diff_field_attr = [];

    /**
     * 单利模式实例化
     *
     * @param \mysqli $mysqli
     *
     * @return DBManager
     */
    public static function getIntance($mysqli)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($mysqli);
        }
        return self::$instance;
    }

    /**
     * 初始化检测是否为有效mysqli
     *
     * @param \mysqli $mysqli mysqli实例
     *
     * @return \mysqli
     */
    private function __construct($mysqli)
    {
        if (!$mysqli instanceof \mysqli) {
            die('not found obj from mysqli!'); // 输出到控制台
        }
        $this->mysqli = $mysqli;
        return $this->mysqli;
    }

    /**
     * 数据库是否存在
     *
     * @param string $db_name 数据库名
     *
     * @return boolean|DBManager
     */
    public function dbIsExists($db_name)
    {
        if (!$db_name = trim($db_name)) {
            die('db name is not empty!'); // 输出到控制台
        }
        $mysqli_result = $this->mysqli->query('SHOW DATABASES');
        $flag = false;
        while ($row = $mysqli_result->fetch_assoc()) {
            if ($row['Database'] == $db_name) {
                $flag = true;
                break;
            }
        }
        if (!$flag) {
            $this->diff_db[] = $db_name;
            return $flag;
        }
        $this->select_db = $db_name;
        $this->mysqli->select_db($db_name);
        return $this;
    }

    /**
     * 得到当前数据库所有的表
     *
     * @param string $db_name 数据库名称
     *
     * @return array
     */
    public function allTableFromDB($db_name)
    {
        if (!$db_name = trim($db_name)) {
            die('db name is not empty!'); // 输出到控制台
        }
        $mysqli_result = $this->mysqli->query('SHOW TABLES');
        $ret = [];
        while ($row = $mysqli_result->fetch_assoc()) {
            $ret[] = $row['Tables_in_test'];
        }
        return $ret;
    }

}