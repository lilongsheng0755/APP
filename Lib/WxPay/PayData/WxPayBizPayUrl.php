<?php

namespace Lib\WxPay\PayData;
/**
 * Author: lilongsheng
 * CreateTime: 2019/9/5 16:09
 * Description: 扫码支付模式一生成二维码请求字段处理
 */
class WxPayBizPayUrl extends WxPayData
{
    /**
     * 设置微信分配的公众账号ID
     *
     * @param string $value
     *
     * @return WxPayBizPayUrl
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
     */
    public function isAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }


    /**
     * 设置微信支付分配的商户号
     *
     * @param string $value
     *
     * @return WxPayBizPayUrl
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
     */
    public function isMchIdSet()
    {
        return array_key_exists('mch_id', $this->values);
    }

    /**
     * 设置支付时间戳
     *
     * @param string $value
     *
     * @return WxPayBizPayUrl
     */
    public function setTimeStamp($value)
    {
        $this->values['time_stamp'] = $value;
        return $this;
    }

    /**
     * 获取支付时间戳的值
     *
     * @return mixed
     */
    public function getTimeStamp()
    {
        return $this->values['time_stamp'];
    }

    /**
     * 判断支付时间戳是否存在
     *
     * @return bool
     */
    public function isTimeStampSet()
    {
        return array_key_exists('time_stamp', $this->values);
    }

    /**
     * 设置随机字符串
     *
     * @param string $value
     *
     * @return WxPayBizPayUrl
     */
    public function setNonceStr($value)
    {
        $this->values['nonce_str'] = $value;
        return $this;
    }

    /**
     * 获取随机字符串的值
     *
     * @return mixed
     */
    public function getNonceStr()
    {
        return $this->values['nonce_str'];
    }

    /**
     * 判断随机字符串是否存在
     *
     * @return bool
     */
    public function isNonceStrSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }

    /**
     * 设置商品ID
     *
     * @param string $value
     *
     * @return WxPayBizPayUrl
     */
    public function setProductId($value)
    {
        $this->values['product_id'] = $value;
        return $this;
    }

    /**
     * 获取商品ID的值
     *
     * @return mixed
     */
    public function getProductId()
    {
        return $this->values['product_id'];
    }

    /**
     * 判断商品ID是否存在
     *
     * @return bool
     */
    public function isProductIdSet()
    {
        return array_key_exists('product_id', $this->values);
    }

    /**
     * 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
     *
     * @param string $value
     *
     * @return WxPayBizPayUrl
     */
    public function setTradeType($value)
    {
        $this->values['trade_type'] = $value;
        return $this;
    }

    /**
     * 获取取值如下：JSAPI，NATIVE，APP，详细说明见参数规定的值
     *
     * @return mixed
     */
    public function getTradeType()
    {
        return $this->values['trade_type'];
    }

    /**
     * 判断取值如下：JSAPI，NATIVE，APP，详细说明见参数规定是否存在
     *
     * @return bool
     **/
    public function isTradeTypeSet()
    {
        return array_key_exists('trade_type', $this->values);
    }

    /**
     * 继承单利模式
     *
     * @return WxPayData|object|WxPayBizPayUrl
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }
}