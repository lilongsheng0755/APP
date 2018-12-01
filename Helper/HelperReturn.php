<?php

namespace Helper;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-8-27 22:37:33
 * Description: 接口返回定义类
 */
class HelperReturn {

    /**
     * 返回json数据格式
     * 
     * @param array $data  需要返回的数据
     * @param int $code 默认0正常，负数为异常错误，100-999为业务逻辑错误
     */
    public static function jsonData($data = array(), $code = 0) {
        $ret = array('code' => 0, 'data' => array());
        $ret['code'] = (int) $code;
        $ret['data'] = $data ? (array) $data : array();
        die(json_encode($ret));
    }

}
