<?php

namespace Helper;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:24:00
 * Description: 数字类型处理
 */
class HelperNumber {

    /**
     * 强转整型
     * 
     * @param int|string $num
     * @return int
     */
    public static function uint($num) {
        if ((!$num = trim($num)) || !is_numeric($num)) {
            return 0;
        }
        return (int) $num;
    }

}
