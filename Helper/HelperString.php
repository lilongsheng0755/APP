<?php

namespace Helper;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:24:24
 * Description: 字符串处理类
 */
class HelperString {

    public static function ip2long($ip) {
        $long = ip2long($ip);
        if ($long == - 1 || $long === false) {
            return 0;
        }
        return sprintf("%u", $long);
    }

}
