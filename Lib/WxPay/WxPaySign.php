<?php

namespace Lib\WxPay;
/**
 * Author: lilongsheng
 * CreateTime: 2019/9/2 11:24
 * Description: 微信支付签名算法
 */
class WxPaySign
{
    /**
     * md5签名算法
     */
    const SIGN_TYPE_MD5 = 1;

    /**
     * SHA256签名算法
     */
    const SIGN_TYPE_SHA256 = 2;

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

}