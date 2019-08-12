<?php

namespace Helper;

/**
 * Author: skylong
 * CreateTime: 2018-8-27 22:37:33
 * Description: 接口返回数据辅助类
 */
class HelperReturn
{

    /**
     * 返回json数据格式
     *
     * @param array $data 需要返回的数据
     * @param int   $code 默认0正常，负数为异常错误，100-999为业务逻辑错误
     */
    public static function jsonData($data = [], $code = 0)
    {
        header('Content-Type:application/json;charset=utf-8');
        $ret = ['code' => 0, 'data' => []];
        $ret['code'] = (int)$code;
        $ret['data'] = $data ? (array)$data : [];
        die(json_encode($ret));
    }

}
