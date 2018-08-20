<?php

namespace lib\db;

/**
 * Author: skylong
 * CreateTime: 2018-8-20 18:28:05
 * Description: 数据库抽象类
 */
abstract class DataBase {

    abstract public function connect();

    abstract public function insert();

    abstract public function delete();

    abstract public function update();

    abstract public function select();
}
