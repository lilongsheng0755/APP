<?php


namespace Lib\SPL\SplInterface;

/**
 * Author: skylong
 * CreateTime: 2019/8/28 10:22
 * Description: 配置接口规范定义
 */
interface IConfigDB
{
    /**
     * 系统DB索引
     */
    const DB_INDEX_SYSTEM = 1;

    /**
     * 本地环境DB配置
     *
     * @param int $db_index
     *
     * @return mixed
     */
    public static function getLocalConfig($db_index = self::DB_INDEX_SYSTEM);

    /**
     * 外网测试环境DB配置
     *
     * @param int $db_index
     *
     * @return mixed
     */
    public static function getDevConfig($db_index = self::DB_INDEX_SYSTEM);

    /**
     * 生产环境DB配置
     *
     * @param int $db_index
     *
     * @return mixed
     */
    public static function getProConfig($db_index = self::DB_INDEX_SYSTEM);
}