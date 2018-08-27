<?php

namespace lib\db;

/**
 * Author: skylong
 * CreateTime: 2018-8-20 18:28:05
 * Description: 数据库抽象类
 */
abstract class DataBase {

    /**
     * 执行一条SQL语句
     */
    abstract protected function query($sql);

    /**
     *  获取一条记录
     */
    abstract protected function getOne($sql, $result_type);

    /**
     * 获取多条记录
     */
    abstract protected function getAll($sql, $result_type);

    /**
     * 获取一个数据对象
     */
    abstract protected function getOneObject($sql, $class_name);

    /**
     * 获取多个数据对象
     */
    abstract protected function getAllObject($sql, $class_name);

    /**
     * 返回影响行数
     */
    abstract protected function affectedRows();

    /**
     * 返回最新自增ID
     */
    abstract protected function insertID();

    /**
     * 开启一个事务,只对InnoDB表起作用
     */
    abstract protected function startTransaction();

    /**
     * 提交事务
     */
    abstract protected function commit();

    /**
     * 回滚事务
     */
    abstract protected function rollback();
    
    /**
     * 获取当前查询返回记录数
     */
    abstract protected function getNumRows();
}
