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
     * @param array  $data     需要返回的数据
     * @param int    $err_code 默认0正常，负数为异常错误，100-999为业务逻辑错误
     * @param string $err_msg  错误信息
     */
    public static function jsonData($data = [], $err_code = 0, $err_msg = '')
    {
        header('Content-Type:application/json;charset=utf-8');
        $ret = ['err_code' => 0, 'err_msg' => '', 'data' => []];
        $ret['err_code'] = (int)$err_code;
        $ret['err_msg'] = (string)$err_msg;
        $ret['data'] = $data ? (array)$data : [];
        exit(json_encode($ret));
    }

}
