<?php

namespace Lib\WxPay;
/**
 * Author: lilongsheng
 * CreateTime: 2019/9/2 14:06
 * Description:微信支付回调处理类
 */
class WxPayNotify extends WxPayResults
{
    /**
     *
     * 设置错误码 FAIL 或者 SUCCESS
     *
     * @param string
     */
    public function setReturnCode($return_code)
    {
        $this->values['return_code'] = $return_code;
    }

    /**
     *
     * 获取错误码 FAIL 或者 SUCCESS
     *
     * @return string $return_code
     */
    public function getReturnCode()
    {
        return $this->values['return_code'];
    }

    /**
     *
     * 设置错误信息
     *
     * @param string $return_msg
     */
    public function setReturnMsg($return_msg)
    {
        $this->values['return_msg'] = $return_msg;
    }

    /**
     *
     * 获取错误信息
     *
     * @return string
     */
    public function getReturnMsg()
    {
        return $this->values['return_msg'];
    }

}