<?php

namespace Lib\WxPay;


/**
 * Author: lilongsheng
 * CreateTime: 2019/9/4 13:45
 * Description:微信支付返回结果处理
 */
class WxPayResults extends WxPayData
{
    /**
     * 校验xml数据并转成数组
     *
     * @param $xml
     *
     * @return array|bool
     * @throws WxPayException
     */
    public static function init()
    {
        $obj = new self();
        $obj->fromXml(file_get_contents('php://input'));
        //失败则直接返回失败
        if ($obj->values['return_code'] != 'SUCCESS') {
            foreach ($obj->values as $key => $value) {
                #除了return_code和return_msg之外其他的参数存在，则报错
                if ($key != "return_code" && $key != "return_msg") {
                    throw new WxPayException("支付返回数据存在异常！");
                }
            }
            return $obj->getValues();
        }
        $obj->checkSign();
        return $obj->getValues();
    }

    /**
     *
     * 使用数组初始化
     *
     * @param array $array
     */
    public function fromArray($array)
    {
        $this->values = $array;
    }


    /**
     *
     * 设置参数
     *
     * @param string $key
     * @param string $value
     */
    public function setData($key, $value)
    {
        $this->values[$key] = $value;
    }
}