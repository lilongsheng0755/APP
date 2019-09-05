<?php

namespace Lib\WxPay\PayResults;

use Lib\WxPay\PayApi\WxPayApi;

/**
 * Author: lilongsheng
 * CreateTime: 2019/9/2 14:06
 * Description:微信支付回调处理类
 */
class WxPayNotify extends WxPayResults
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
     *
     * 回调入口
     * @param bool $needSign  是否需要签名返回
     */
    final public function Handle($config, $needSign = true)
    {
        $this->config = $config;
        $msg = "OK";
        //当返回false的时候，表示notify中调用NotifyCallBack回调失败获取签名校验失败，此时直接回复失败
        $result = WxpayApi::notify($config, array($this, 'NotifyCallBack'), $msg);
        if($result == false){
            $this->SetReturn_code("FAIL");
            $this->SetReturn_msg($msg);
            $this->ReplyNotify(false);
            return;
        } else {
            //该分支在成功回调到NotifyCallBack方法，处理完成之后流程
            $this->SetReturn_code("SUCCESS");
            $this->SetReturn_msg("OK");
        }
        $this->ReplyNotify($needSign);
    }

    /**
     *
     * 回调方法入口，子类可重写该方法
    //TODO 1、进行参数校验
    //TODO 2、进行签名验证
    //TODO 3、处理业务逻辑
     * 注意：
     * 1、微信回调超时时间为2s，建议用户使用异步处理流程，确认成功之后立刻回复微信服务器
     * 2、微信服务器在调用失败或者接到回包为非确认包的时候，会发起重试，需确保你的回调是可以重入
     * @param WxPayNotifyResults $objData 回调解释出的参数
     * @param WxPayConfigInterface $config
     * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
     * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($objData, $config, &$msg)
    {
        //TODO 用户基础该类之后需要重写该方法，成功的时候返回true，失败返回false
        return false;
    }

    /**
     *
     * 业务可以继承该方法，打印XML方便定位.
     * @param string $xmlData 返回的xml参数
     *
     **/
    public function LogAfterProcess($xmlData)
    {
        return;
    }

    /**
     *
     * notify回调方法，该方法中需要赋值需要输出的参数,不可重写
     * @param array $data
     * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    final public function NotifyCallBack($data)
    {
        $msg = "OK";
        $result = $this->NotifyProcess($data, $this->config, $msg);

        if($result == true){
            $this->SetReturn_code("SUCCESS");
            $this->SetReturn_msg("OK");
        } else {
            $this->SetReturn_code("FAIL");
            $this->SetReturn_msg($msg);
        }
        return $result;
    }

    /**
     *
     * 回复通知
     * @param bool $needSign 是否需要签名输出
     */
    final private function ReplyNotify($needSign = true)
    {
        //如果需要签名
        if($needSign == true &&
            $this->GetReturn_code() == "SUCCESS")
        {
            $this->SetSign($this->config);
        }

        $xml = $this->ToXml();
        $this->LogAfterProcess($xml);
        WxpayApi::replyNotify($xml);
    }

}