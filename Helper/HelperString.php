<?php

namespace Helper;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:24:24
 * Description: 字符串处理类
 */
class HelperString {

    /**
     * IP地址转换成整型,数据库保存类型为bigint，PHP中的互转函数为long2ip()
     * 
     * @param string $ip
     * @return int|string
     */
    public static function ip2long($ip) {
        $long = ip2long($ip);
        if ($long == - 1 || $long === false) {
            return 0;
        }
        return sprintf("%u", $long);
    }

    /**
     * 去除字符串中的转义，PHP中的互转函数为addcslashes()
     * 
     * @param string $str
     * @return string
     */
    public static function stripslashes($str) {
        if (!$str) {
            return '';
        }
        return stripslashes(trim($str));
    }

    /**
     * 反斜线引用字符串单引号（'）、双引号（"）、反斜线（\）与 NUL（ NULL  字符），PHP中的互转函数为stripslashes()
     * 
     * @param string $str
     * @return string
     */
    public static function addslashes($str) {
        if (!$str) {
            return '';
        }
        return addslashes(trim($str));
    }

    /**
     * 只保留字符串中的中文部分
     * 
     * @param string $str
     * @return string
     */
    public static function keepOnlyZh($str) {
        if (!$str = trim($str)) {
            return '';
        }
        $matches = array();
        preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $str, $matches);
        $str = implode('', $matches[0]);
        return $str ? $str : '';
    }

    /**
     * 过滤微信emoji表情
     * 如果subject是一个数组， 返回一个数组，其他情况返回字符串。错误发生时返回 NULL 。
     * 
     * @param string $str
     * @return string
     */
    public static function filterEmoji($str) {
        if (!$str = trim($str)) {
            return '';
        }
        //执行一个正则表达式搜索并且使用一个回调进行替换 
        $substr = preg_replace_callback("/(\\\ud[0-9a-f]{3})|(\\\ue[0-9a-f]{3})/", function() {
            return '';
        }, $str);
        return $substr ? $substr : '';
    }

    /**
     * 中文字符串截取
     * 
     * @param string $str 需要截取的中文字符串
     * @param int $start 截取开始的位置，默认=0，第一位开始
     * @param int $length 需要截取的长度
     * @param string $charset 字符串编码，utf-8 / UTF8
     * @param boolean $suffix 是否尾缀 ... 省略符号，默认 true
     * @return string
     */
    public static function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
        if (!$str = trim($str)) {
            return '';
        }
        $start = (int) $start;
        $length = (int) $length;
        switch ($charset) {
            case 'utf-8':
                $char_len = 3;
                break;
            case 'UTF8':
                $char_len = 3;
                break;
            default:
                $char_len = 2;
                break;
        }
        //小于指定长度，直接返回
        if (strlen($str) <= ($length * $char_len)) {
            return $str;
        }
        if (function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $charset);
        } elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            $match = array();
            preg_match_all($re[$charset], $str, $match);
            $slice = implode('', array_slice($match[0], $start, $length));
        }
        if ($suffix) {
            return $slice . "…";
        }
        return $slice;
    }

}
