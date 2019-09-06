<?php

namespace Lib\WxPay\PayResults;

use Lib\WxPay\PayApi\WxPayApi;
use Lib\WxPay\PayData\WxPayData;

/**
 * Author: lilongsheng
 * CreateTime: 2019/9/2 14:06
 * Description:微信支付回调处理类
 */
class WxPayNotify extends WxPayData
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

    /**
     * 初始化回调数据
     *
     * @param string $xml
     *
     * @return array|bool|WxPayNotify
     * @throws \Lib\WxPay\PayException\WxPayException
     */
    public static function init($xml)
    {
        $obj = new self();
        $obj->fromXml($xml);
        //失败则直接返回失败
        $obj->checkSign();
        return $obj;
    }

    /**
     * 回调入口
     *
     * @param bool $needSign 是否需要签名返回
     *
     * @return bool
     * @throws \Lib\WxPay\PayException\WxPayException
     */
    public function handle($needSign = true)
    {
        $msg = "OK";
        //当返回false的时候，表示notify中调用NotifyCallBack回调失败获取签名校验失败，此时直接回复失败
        $result = WxpayApi::notify([$this, 'notifyCallBack'], $msg);
        if ($result == false) {
            $this->setReturnCode("FAIL");
            $this->setReturnMsg($msg);
            $this->replyNotify(false);
            return true;
        } else {
            //该分支在成功回调到NotifyCallBack方法，处理完成之后流程
            $this->setReturnCode("SUCCESS");
            $this->setReturnMsg("OK");
        }
        $this->replyNotify($needSign);
        return true;
    }

    /**
     * 回调方法入口，子类可重写该方法
     * //TODO 1、进行参数校验
     * //TODO 2、进行签名验证
     * //TODO 3、处理业务逻辑
     * 注意：
     * 1、微信回调超时时间为2s，建议用户使用异步处理流程，确认成功之后立刻回复微信服务器
     * 2、微信服务器在调用失败或者接到回包为非确认包的时候，会发起重试，需确保你的回调是可以重入
     *
     * @param array  $data 回调解释出的参数
     * @param string $msg  如果回调处理失败，可以将错误信息输出到该方法
     *
     * @return bool
     */
    public function notifyProcess($data, &$msg)
    {
        //TODO 用户继承该类之后需要重写该方法，成功的时候返回true，失败返回false
        return false;
    }

    /**
     * 业务可以继承该方法，打印XML方便定位.
     *
     * @param string $xmlData 返回的xml参数
     *
     * @return bool
     */
    public function logAfterProcess($xmlData)
    {
        return true;
    }

    /**
     * notify回调方法，该方法中需要赋值需要输出的参数,不可重写
     *
     * @param array $data 回调数据
     *
     * @return bool
     */
    final public function notifyCallBack($data)
    {
        $msg = "OK";
        $result = $this->notifyProcess($data, $msg);

        if ($result == true) {
            $this->setReturnCode("SUCCESS");
            $this->setReturnMsg("OK");
        } else {
            $this->setReturnCode("FAIL");
            $this->setReturnMsg($msg);
        }
        return $result;
    }

    /**
     * @param bool $needSign
     *
     * @throws \Lib\WxPay\PayException\WxPayException
     */
    final private function replyNotify($needSign = true)
    {
        //如果需要签名
        if ($needSign == true &&
            $this->getReturnCode() == "SUCCESS") {
            $this->setSign($this->makeSign());
        }

        $xml = $this->toXml();
        $this->logAfterProcess($xml);
        WxpayApi::replyNotify($xml);
    }

}