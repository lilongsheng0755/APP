<?php

namespace Lib\WxPay\PayData;


/**
 * Author: lilongsheng
 * CreateTime: 2019/9/4 18:01
 * Description:订单查询请求字段处理
 */
class WxPayOrderQuery extends WxPayData
{
    /**
     * 设置微信分配的公众账号ID
     *
     * @param string $value
     *
     * @return WxPayOrderQuery
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
     */
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
     * @return WxPayOrderQuery
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
     */
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
     * 设置微信的订单号，优先使用
     *
     * @param string $value
     *
     * @return WxPayOrderQuery
     */
    public function setTransactionId($value)
    {
        $this->values['transaction_id'] = $value;
        return $this;
    }

    /**
     * 获取微信的订单号，优先使用的值
     *
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->values['transaction_id'];
    }

    /**
     * 判断微信的订单号，优先使用是否存在
     *
     * @return bool
     **/
    public function isTransactionIdSet()
    {
        return array_key_exists('transaction_id', $this->values);
    }


    /**
     * 设置商户系统内部的订单号，当没提供transaction_id时需要传这个。
     *
     * @param string $value
     *
     * @return WxPayOrderQuery
     */
    public function setOutTradeNo($value)
    {
        $this->values['out_trade_no'] = $value;
        return $this;
    }

    /**
     * 获取商户系统内部的订单号，当没提供transaction_id时需要传这个。的值
     *
     * @return mixed
     */
    public function getOutTradeNo()
    {
        return $this->values['out_trade_no'];
    }

    /**
     * 判断商户系统内部的订单号，当没提供transaction_id时需要传这个。是否存在
     *
     * @return bool
     **/
    public function isOutTradeNoSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }


    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     *
     * @param string $value
     *
     * @return WxPayOrderQuery
     */
    public function setNonceStr($value)
    {
        $this->values['nonce_str'] = $value;
        return $this;
    }

    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     *
     * @return mixed
     */
    public function getNonceStr()
    {
        return $this->values['nonce_str'];
    }

    /**
     * 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
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
     * @return WxPayData|object|WxPayOrderQuery
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }
}