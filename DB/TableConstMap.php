<?php
/**
 * Author: skylong
 * CreateTime: 2019/12/11 11:20
 * Description: 数据表常量定义
 */

namespace DB;


use Lib\SPL\SplAbstract\AReflectionClass;

class TableConstMap extends AReflectionClass
{

    /**
     * 表引擎：InnoDB
     */
    const TABLE_ENGINE_INNODB = 'InnoDB';

    /**
     * 表引擎：MyISAM
     */
    const TABLE_ENGINE_MYISAM = 'MyISAM';

    /**
     * 表编码：utf8
     */
    const TABLE_CHARSET_UTF8 = 'utf8';

    /**
     * 表编码：utf8mb4
     */
    const TABLE_CHARSET_UTF8MB4 = 'utf8mb4';

    /**
     * 字段类型：int
     */
    const FIELD_TYPE_INT = 'int';

    /**
     * 数据类型：bigint
     */
    const FIELD_TYPE_BIGINT = 'bigint';

    /**
     * 数据类型：tinyint
     */
    const FIELD_TYPE_TINYINT = 'tinyint';

    /**
     * 数据类型：smallint
     */
    const FIELD_TYPE_SMALLINT = 'smallint';

    /**
     * 数据类型：char
     */
    const FIELD_TYPE_CHAR = 'char';

    /**
     * 数据类型：varchar
     */
    const FIELD_TYPE_VARCHAR = 'varchar';

    /**
     * 数据类型：date
     */
    const FIELD_TYPE_DATE = 'date';

    /**
     * 数据类型：datetime
     */
    const FIELD_TYPE_DATETIME = 'datetime';

    /**
     * 数据类型：year
     */
    const FIELD_TYPE_YEAR = 'year';

    /**
     * 数据类型：decimal
     */
    const FIELD_TYPE_DECIMAL = 'decimal';

    /**
     * 数据类型：text
     */
    const FIELD_TYPE_TEXT = 'text';

    /**
     * 得到允许的表引擎
     *
     * @return array
     */
    public static function getAllowTableEngine()
    {
        $ret = [];
        $reflection = self::getReflectionClass();
        $const = $reflection->getConstants();
        foreach ($const as $key => $value) {
            if (strrpos($key, 'TABLE_ENGINE_') === false) {
                continue;
            }
            $ret[] = $value;
        }
        return $ret;
    }

    /**
     * 得到允许的编码
     *
     * @return array
     */
    public static function getAllowTableCharset()
    {
        $ret = [];
        $reflection = self::getReflectionClass();
        $const = $reflection->getConstants();
        foreach ($const as $key => $value) {
            if (strrpos($key, 'TABLE_CHARSET_') === false) {
                continue;
            }
            $ret[] = $value;
        }
        return $ret;
    }

    /**
     * 得到允许的字段类型
     *
     * @return array
     */
    public static function getAllowFieldType()
    {
        $ret = [];
        $reflection = self::getReflectionClass();
        $const = $reflection->getConstants();
        foreach ($const as $key => $value) {
            if (strrpos($key, 'FIELD_TYPE_') === false) {
                continue;
            }
            $ret[] = $value;
        }
        return $ret;
    }

}