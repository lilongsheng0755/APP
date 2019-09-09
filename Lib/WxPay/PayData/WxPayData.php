<?php

namespace Lib\WxPay\PayData;

use Lib\SPL\SplAbstract\ASingleBase;
use Lib\WxPay\PayConfig\WxPayConfig;
use Lib\WxPay\PayException\WxPayException;

/**
 * Author: lilongsheng
 * CreateTime: 2019/9/2 14:07
 * Description: 微信支付数据格式处理
 */
class WxPayData extends ASingleBase
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
     * @param string $sign_type MD5|HMAC-SHA256
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
     * 生成签名
     *
     * @param bool $need_sign_type 是否需要补 sign_type
     *
     * @return string
     * @throws WxPayException
     */
    public function makeSign($need_sign_type = true)
    {
        if ($need_sign_type) {
            $this->setSignType(WxPayConfig::getInstance()->getSignType());
        }

        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->ToUrlParams();

        //签名步骤二：在string后加入KEY
        $sign_type = WxPayConfig::getInstance()->getSignType();
        $pay_key = WxPayConfig::getInstance()->getPayKey();
        $string = $string . "&key=" . $pay_key;

        //签名步骤三：MD5加密或者HMAC-SHA256
        switch ($sign_type) {
            case 'MD5':
                $string = md5($string);
                break;
            case 'HMAC-SHA256':
                $string = hash_hmac("sha256", $string, $pay_key);
                break;
            default:
                throw new WxPayException("签名类型不支持！");
        }

        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 签名校验
     *
     * @return bool
     * @throws WxPayException
     */
    public function checkSign()
    {
        if (!$this->isSetSign()) {
            throw new WxPayException("未提供签名！");
        }

        $sign = $this->makeSign(false);
        if ($this->getSign() == $sign) {
            //签名正确
            return true;
        }
        throw new WxPayException("签名错误！");
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