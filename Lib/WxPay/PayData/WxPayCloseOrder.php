<?php

namespace Lib\WxPay\PayData;
/**
 * Author: lilongsheng
 * CreateTime: 2019/9/5 11:47
 * Description: 关闭订单请求字段处理
 */
class WxPayCloseOrder extends WxPayData
{
    /**
     * 设置微信分配的公众账号ID
     *
     * @param string $value
     *
     * @return WxPayCloseOrder
     */
    public function setAppid($value)
    {
        $this->values['appid'] = $value;
        return $this;
    }

    /**
     * 获取微信分配的公众账号ID的值
     *
     * @return mixed
     **/
    public function getAppid()
    {
        return $this->values['appid'];
    }

    /**
     * 判断微信分配的公众账号ID是否存在
     *
     * @return bool
     **/
    public function isAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }


    /**
     * 设置微信支付分配的商户号
     *
     * @param string $value
     *
     * @return WxPayCloseOrder
     */
    public function setMchId($value)
    {
        $this->values['mch_id'] = $value;
        return $this;
    }

    /**
     * 获取微信支付分配的商户号的值
     *
     * @return mixed
     **/
    public function getMchId()
    {
        return $this->values['mch_id'];
    }

    /**
     * 判断微信支付分配的商户号是否存在
     *
     * @return bool
     **/
    public function isMchIdSet()
    {
        return array_key_exists('mch_id', $this->values);
    }


    /**
     * 设置商户系统内部的订单号
     *
     * @param string $value
     *
     * @return WxPayCloseOrder
     */
    public function setOutTradeNo($value)
    {
        $this->values['out_trade_no'] = $value;
        return $this;
    }

    /**
     * 获取商户系统内部的订单号的值
     *
     * @return mixed
     **/
    public function getOutTradeNo()
    {
        return $this->values['out_trade_no'];
    }

    /**
     * 判断商户系统内部的订单号是否存在
     *
     * @return bool
     **/
    public function isOutTradeNoSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }


    /**
     * 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
     *
     * @param string $value
     *
     * @return WxPayCloseOrder
     */
    public function setNonceStr($value)
    {
        $this->values['nonce_str'] = $value;
        return $this;
    }

    /**
     * 获取商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号的值
     *
     * @return mixed
     **/
    public function getNonceStr()
    {
        return $this->values['nonce_str'];
    }

    /**
     * 判断商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号是否存在
     *
     * @return bool
     **/
    public function isNonceStrSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }

    /**
     * 继承单利模式
     *
     * @return WxPayData|object|WxPayCloseOrder
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }
}