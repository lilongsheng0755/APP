<?php
/**
 * Author: skylong
 * CreateTime: 2019/12/11 11:18
 * Description: 数据表结构设计类
 */

namespace DB;

class TableDesign
{

    /**
     * 表名称
     *
     * @var string
     */
    private $table_name = '';

    /**
     * 表注释
     *
     * @var string
     */
    private $table_notes = '';

    /**
     * 数据表引擎
     *
     * @var string
     */
    private $table_engine = 'InnoDB';

    /**
     * 数据表默认编码
     *
     * @var string
     */
    private $table_default_charset = 'utf8mb4';

    /**
     * 主键名
     *
     * @var array
     */
    private $primary_key = [];

    /**
     * 字段唯一键
     *
     * @var array
     */
    private $unique_key = [];

    /**
     * 字段索引
     *
     * @var array
     */
    private $index_key = [];

    /**
     * 字段列表
     *
     * @var array
     */
    private $field_attr_list = [];

    /**
     * 获取表名称
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * 设置表名称
     *
     * @param string $table_name
     *
     * @return boolean|$this
     */
    public function setTableName($table_name = '')
    {
        if (!$table_name = trim($table_name)) {
            return false;
        }
        $this->table_name = $table_name;
        return $this;
    }

    /**
     * 获取表注释
     *
     * @return string
     */
    public function getTableNotes()
    {
        return $this->table_notes;
    }

    /**
     * 设置表注释
     *
     * @param string $table_notes
     *
     * @return boolean|$this
     */
    public function setTableNotes($table_notes = '')
    {
        if (!$table_notes = trim($table_notes)) {
            return false;
        }
        $this->table_notes = $table_notes;
        return $this;
    }

    /**
     * 获取表引擎
     *
     * @return string
     */
    public function getTableEngine()
    {
        return $this->table_engine;
    }

    /**
     * 设置表引擎
     *
     * @param string $table_engine
     *
     * @return boolean|$this
     */
    public function setTableEngine($table_engine = '')
    {
        if ((!$table_engine = trim($table_engine)) || !in_array($table_engine, TableConstMap::getAllowTableEngine())) {
            return false;
        }
        $this->table_engine = $table_engine;
        return $this;
    }

    /**
     * 获取表默认编码
     *
     * @return string
     */
    public function getTableDefaultCharset()
    {
        return $this->table_default_charset;
    }

    /**
     * 设置表默认编码
     *
     * @param string $table_default_charset
     *
     * @return boolean|$this
     */
    public function setTableDefaultCharset($table_default_charset = '')
    {
        if ((!$table_default_charset = trim($table_default_charset)) || !in_array($table_default_charset, TableConstMap::getAllowTableCharset())) {
            return false;
        }
        $this->table_default_charset = $table_default_charset;
        return $this;
    }

    /**
     * 获取表字段属性列表
     *
     * @return array
     */
    public function getTableFieldAttrList()
    {
        return $this->field_attr_list;
    }

    /**
     * 设置表字段相关属性
     *
     * @param string  $field_name     字段名
     * @param string  $field_type     字段类型
     * @param string  $field_length   长度
     * @param boolean $is_null        是否为空
     * @param mixed   $default_val    默认值
     * @param mixed   $comment        字段描述
     * @param boolean $is_unsigned    是否带符号
     * @param boolean $auto_increment 是否自增
     *
     * @return boolean|$this
     */
    public function setTableFieldAttr($field_name = '', $field_type = '', $field_length = '', $is_null = false, $default_val = null, $comment = '', $is_unsigned = false, $auto_increment = false)
    {
        if ((!$field_name = trim($field_name)) || !in_array($field_type, TableConstMap::getAllowFieldType())) {
            return false;
        }

        $this->field_attr_list[$field_name]['field_type'] = $field_type;
        $this->field_attr_list[$field_name]['field_length'] = $field_length;
        $this->field_attr_list[$field_name]['is_null'] = $is_null;
        $this->field_attr_list[$field_name]['default_val'] = $default_val;
        $this->field_attr_list[$field_name]['comment'] = $comment;
        $this->field_attr_list[$field_name]['is_unsigned'] = $is_unsigned;
        $this->field_attr_list[$field_name]['auto_increment'] = $auto_increment;

        return $this;
    }

    /**
     * 获取主键配置
     *
     * @return array
     */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    /**
     * 设置主键
     *
     * @param array $primary_field 主键字段，支持多个字段联合主键
     *
     * @return boolean|$this
     */
    public function setPrimaryKey($primary_field = [])
    {
        if (!$primary_field || !is_array($primary_field) || !$this->field_attr_list) {
            return false;
        }

        foreach ($primary_field as $field) {
            if (!isset($this->field_attr_list[$field])) {
                return false;
            }
        }
        $this->primary_key = $primary_field;
        return $this;
    }

    /**
     * 获取唯一键配置
     *
     * @return array
     */
    public function getUniqueKey()
    {
        return $this->unique_key;
    }

    /**
     * 设置唯一键
     *
     * @param string $unique_key_name 唯一键名称
     * @param array  $unique_field    唯一键字段，支持多个字段联合唯一键
     *
     * @return boolean|$this
     */
    public function setUniqueKey($unique_key_name = '', $unique_field = [])
    {
        if ((!$unique_key_name = trim($unique_key_name)) || !$unique_field || !is_array($unique_field) || !$this->field_attr_list) {
            return false;
        }

        foreach ($unique_field as $field) {
            if (!isset($this->field_attr_list[$field])) {
                return false;
            }
        }
        $this->unique_key[$unique_key_name] = $unique_field;
        return $this;
    }

    /**
     * 获取索引键配置
     *
     * @return array
     */
    public function getIndexKey()
    {
        return $this->index_key;
    }

    /**
     * 设置索引键
     *
     * @param string $index_key_name 索引键名称
     * @param array  $index_field    索引键字段，支持多个字段联合索引
     *
     * @return boolean|$this
     */
    public function setIndexKey($index_key_name = '', $index_field = [])
    {
        if ((!$index_key_name = trim($index_key_name)) || !$index_field || !is_array($index_field) || !$this->field_attr_list) {
            return false;
        }

        foreach ($index_field as $field) {
            if (!isset($this->field_attr_list[$field])) {
                return false;
            }
        }
        $this->index_key[$index_key_name] = $index_field;
        return $this;
    }

}