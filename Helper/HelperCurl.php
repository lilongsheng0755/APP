<?php

namespace Helper;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-12-1 11:13:04
 * Description: 基于curl扩展辅助类
 */
class HelperCurl {

    public static function curl($url, $method = 'post', $data = array(), $header_type = 'json') {
        if ((!$url = trim($url)) || !in_array($method, self::getMethod())) {
            return false;
        }
        $ch = curl_init($url);
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'api=' . $api);

        array_push($header, 'Content-Type:application/json');
        array_push($header, 'Accept:application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //curl设置header头

        $ret = curl_exec($ch); //成功时返回 TRUE ， 或者在失败时返回 FALSE 。 然而，如果 CURLOPT_RETURNTRANSFER 选项被设置，函数执行成功时会返回执行的结果

        $err = curl_error($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
    }

    /**
     * curl请求方式
     * 
     * @return array
     */
    private static function getMethod() {
        return array('get', 'post');
    }

}
