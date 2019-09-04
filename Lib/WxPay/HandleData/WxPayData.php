<?php

namespace Lib\WxPay\HandleData;

/**
 * Author: lilongsheng
 * CreateTime: 2019/9/2 14:07
 * Description: 微信支付数据格式处理
 */
class WxPayData
{
    /**
     * 微信支付基础数据
     *
     * @var array
     */
    protected $values = [];

    /**
     * 设置签名生成算法类型
     *
     * @param string $sign_type
     **/
    public function setSignType($sign_type)
    {
        $this->values['sign_type'] = $sign_type;
    }

    /**
     * 获取签名生成算法类型
     *
     * @return mixed
     * @throws WxPayException
     */
    public function getSignType()
    {
        if (!isset($this->values['sign_type']) || !$this->values['sign_type']) {
            throw new WxPayException('未设置签名类型！');
        }

        return $this->values['sign_type'];
    }

    /**
     * 设置生成的签名
     *
     * @param string $sign
     **/
    public function setSign($sign)
    {
        $this->values['sign'] = $sign;
    }

    /**获取生成的签名
     *
     * @return mixed
     * @throws WxPayException
     */
    public function getSign()
    {
        if (!isset($this->values['sign']) || !$this->values['sign']) {
            throw new WxPayException('未生成签名！');
        }
        return $this->values['sign'];
    }

    /**
     * 判断签名是否存在
     *
     * @return bool
     */
    public function isSetSign()
    {
        return array_key_exists('sign', $this->values);
    }

    /**
     * 数组转xml
     *
     * @return string
     * @throws WxPayException
     */
    public function toXml()
    {
        if (!is_array($this->values) || count($this->values) <= 0) {
            throw new WxPayException("数组转xml：数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($this->values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * xml转数组
     *
     * @param string $xml
     *
     * @return array|mixed
     * @throws WxPayException
     */
    public function fromXml($xml)
    {
        if (!$xml) {
            throw new WxPayException("xml转数组：xml数据异常！");
        }
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $this->values;
    }

    /**
     * 格式化参数成url参数
     *
     * @return string
     */
    public function toUrlParams()
    {
        $buff = "";
        foreach ($this->values as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 获取设置的值
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}