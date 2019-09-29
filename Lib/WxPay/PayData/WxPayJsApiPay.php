<?php

namespace Lib\WxPay\PayData;
/**
 * Author: skylong
 * CreateTime: 2019/9/5 16:08
 * Description:提交JSAPI请求字段处理
 */
class WxPayJsApiPay extends WxPayData
{
    /**
     * 设置微信分配的公众账号ID
     *
     * @param string $value
     *
     * @return WxPayJsApiPay
     */
    public function setAppid($value)
    {
        $this->values['appId'] = $value;
        return $this;
    }

    /**
     * 获取微信分配的公众账号ID的值
     *
     * @return mixed
     **/
    public function getAppid()
    {
        return $this->values['appId'];
    }

    /**
     * 判断微信分配的公众账号ID是否存在
     *
     * @return bool
     **/
    public function isAppidSet()
    {
        return array_key_exists('appId', $this->values);
    }


    /**
     * 设置支付时间戳
     *
     * @param string $value
     *
     * @return WxPayJsApiPay
     */
    public function setTimeStamp($value)
    {
        $this->values['timeStamp'] = $value;
        return $this;
    }

    /**
     * 获取支付时间戳的值
     *
     * @return mixed
     **/
    public function getTimeStamp()
    {
        return $this->values['timeStamp'];
    }

    /**
     * 判断支付时间戳是否存在
     *
     * @return bool
     **/
    public function isTimeStampSet()
    {
        return array_key_exists('timeStamp', $this->values);
    }

    /**
     * 随机字符串
     *
     * @param string $value
     *
     * @return WxPayJsApiPay
     */
    public function setNonceStr($value)
    {
        $this->values['nonceStr'] = $value;
        return $this;
    }

    /**
     * 获取notify随机字符串值
     *
     * @return mixed
     **/
    public function getReturnCode()
    {
        return $this->values['nonceStr'];
    }

    /**
     * 判断随机字符串是否存在
     *
     * @return bool
     **/
    public function isReturnCodeSet()
    {
        return array_key_exists('nonceStr', $this->values);
    }


    /**
     * 设置订单详情扩展字符串
     *
     * @param string $value
     *
     * @return WxPayJsApiPay
     */
    public function setPackage($value)
    {
        $this->values['package'] = $value;
        return $this;
    }

    /**
     * 获取订单详情扩展字符串的值
     *
     * @return mixed
     **/
    public function getPackage()
    {
        return $this->values['package'];
    }

    /**
     * 判断订单详情扩展字符串是否存在
     *
     * @return bool
     **/
    public function isPackageSet()
    {
        return array_key_exists('package', $this->values);
    }

    /**
     * 设置签名方式
     *
     * @param string $value
     *
     * @return WxPayJsApiPay
     */
    public function setSignType($value)
    {
        $this->values['signType'] = $value;
        return $this;
    }

    /**
     * 获取签名方式
     *
     * @return mixed
     **/
    public function getSignType()
    {
        return $this->values['signType'];
    }

    /**
     * 判断签名方式是否存在
     *
     * @return true 或 false
     **/
    public function isSignTypeSet()
    {
        return array_key_exists('signType', $this->values);
    }

    /**
     * 设置签名方式
     *
     * @param string $value
     *
     * @return WxPayJsApiPay
     */
    public function setPaySign($value)
    {
        $this->values['paySign'] = $value;
        return $this;
    }

    /**
     * 获取签名方式
     *
     * @return mixed
     **/
    public function getPaySign()
    {
        return $this->values['paySign'];
    }

    /**
     * 判断签名方式是否存在
     *
     * @return bool
     **/
    public function isPaySignSet()
    {
        return array_key_exists('paySign', $this->values);
    }

    /**
     * 继承单利模式
     *
     * @return WxPayData|object|WxPayJsApiPay
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }
}