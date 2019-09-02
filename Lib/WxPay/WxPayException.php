<?php

namespace Lib\WxPay;
/**
 * Author: lilongsheng
 * CreateTime: 2019/9/2 11:03
 * Description: 微信支付异常处理类
 */
class WxPayException extends \Exception
{

    /**
     * 返回异常错误信息
     *
     * @return string
     */
    public function errorMessage()
    {
        return $this->getMessage();
    }
}