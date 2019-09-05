<?php

namespace Lib\WxPay\PayApi;

use Lib\WxPay\PayConfig\WxPayConfig;
use Lib\WxPay\PayData\WxPayUnifiedOrder;
use Lib\WxPay\PayException\WxPayException;
use Lib\WxPay\PayResults\WxPayResults;

/**
 * Author: lilongsheng
 * CreateTime: 2019/9/2 13:34
 * Description:微信支付接口处理类
 *
 * 微信公众平台入口：http://mp.weixin.qq.com
 * 微信开放平台入口：http://open.weixin.qq.com
 * 微信商户平台入口：http://pay.weixin.qq.com
 */
class WxPayApi
{
    /**
     * SDK版本号，更新时间【2019-09-05】
     *
     * @var string
     */
    public static $VERSION = "3.0.10";

    /**
     * @param WxPayUnifiedOrder $input_obj 统一下单对象
     * @param int               $time_out  请求等单秒数
     *
     * @return mixed
     * @throws WxPayException
     */
    public static function unifiedOrder($input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //检测必填参数
        if (!$input_obj->isOutTradeNoSet()) {
            throw new WxPayException("缺少统一支付接口必填参数out_trade_no！");
        } else if (!$input_obj->isBodySet()) {
            throw new WxPayException("缺少统一支付接口必填参数body！");
        } else if (!$input_obj->isTotalFeeSet()) {
            throw new WxPayException("缺少统一支付接口必填参数total_fee！");
        } else if (!$input_obj->isTradeTypeSet()) {
            throw new WxPayException("缺少统一支付接口必填参数trade_type！");
        }

        //关联参数
        if ($input_obj->getTradeType() == "JSAPI" && !$input_obj->isOpenidSet()) {
            throw new WxPayException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
        }
        if ($input_obj->getTradeType() == "NATIVE" && !$input_obj->isProductIdSet()) {
            throw new WxPayException("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
        }

        //异步通知url未设置，则使用配置文件中的url
        if (!$input_obj->isNotifyUrlSet() && WxPayConfig::getInstance()->getNotifyUrl() != "") {
            $input_obj->setNotifyUrl(WxPayConfig::getInstance()->getNotifyUrl());//异步通知url
        }

        $input_obj->setAppid(WxPayConfig::getInstance()->getAppId());//公众账号ID
        $input_obj->setMchId(WxPayConfig::getInstance()->getMerchantId());//商户号
        $input_obj->setSpbillCreateIp(self::getClientIp());//终端ip
        $input_obj->setNonceStr(self::getNonceStr());//随机字符串

        //生成签名
        $input_obj->setSign($input_obj->makeSign(false));
        $xml = $input_obj->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $time_out);
        $result = WxPayResults::init($response);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayConfigInterface $config 配置对象
     * @param WxPayOrderQuery      $input_obj
     * @param int                  $time_out
     *
     * @return 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public static function orderQuery($config, $input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //检测必填参数
        if (!$input_obj->IsOut_trade_noSet() && !$input_obj->IsTransaction_idSet()) {
            throw new WxPayException("订单查询接口中，out_trade_no、transaction_id至少填一个！");
        }
        $input_obj->SetAppid($config->GetAppId());//公众账号ID
        $input_obj->SetMch_id($config->GetMerchantId());//商户号
        $input_obj->SetNonce_str(self::getNonceStr());//随机字符串

        $input_obj->SetSign($config);//签名
        $xml = $input_obj->ToXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $time_out);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 关闭订单，WxPayCloseOrder中out_trade_no必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayConfigInterface $config 配置对象
     * @param WxPayCloseOrder      $input_obj
     * @param int                  $time_out
     *
     * @return 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public static function closeOrder($config, $input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/closeorder";
        //检测必填参数
        if (!$input_obj->IsOut_trade_noSet()) {
            throw new WxPayException("订单查询接口中，out_trade_no必填！");
        }
        $input_obj->SetAppid($config->GetAppId());//公众账号ID
        $input_obj->SetMch_id($config->GetMerchantId());//商户号
        $input_obj->SetNonce_str(self::getNonceStr());//随机字符串

        $input_obj->SetSign($config);//签名
        $xml = $input_obj->ToXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $time_out);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 申请退款，WxPayRefund中out_trade_no、transaction_id至少填一个且
     * out_refund_no、total_fee、refund_fee、op_user_id为必填参数
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayConfigInterface $config 配置对象
     * @param WxPayRefund          $input_obj
     * @param int                  $time_out
     *
     * @return 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public static function refund($config, $input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        //检测必填参数
        if (!$input_obj->IsOut_trade_noSet() && !$input_obj->IsTransaction_idSet()) {
            throw new WxPayException("退款申请接口中，out_trade_no、transaction_id至少填一个！");
        } else if (!$input_obj->IsOut_refund_noSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数out_refund_no！");
        } else if (!$input_obj->IsTotal_feeSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数total_fee！");
        } else if (!$input_obj->IsRefund_feeSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数refund_fee！");
        } else if (!$input_obj->IsOp_user_idSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数op_user_id！");
        }
        $input_obj->SetAppid($config->GetAppId());//公众账号ID
        $input_obj->SetMch_id($config->GetMerchantId());//商户号
        $input_obj->SetNonce_str(self::getNonceStr());//随机字符串

        $input_obj->SetSign($config);//签名
        $xml = $input_obj->ToXml();
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, true, $time_out);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 查询退款
     * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
     * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
     * WxPayRefundQuery中out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayConfigInterface $config 配置对象
     * @param WxPayRefundQuery     $input_obj
     * @param int                  $time_out
     *
     * @return 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public static function refundQuery($config, $input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/refundquery";
        //检测必填参数
        if (!$input_obj->IsOut_refund_noSet() &&
            !$input_obj->IsOut_trade_noSet() &&
            !$input_obj->IsTransaction_idSet() &&
            !$input_obj->IsRefund_idSet()) {
            throw new WxPayException("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！");
        }
        $input_obj->SetAppid($config->GetAppId());//公众账号ID
        $input_obj->SetMch_id($config->GetMerchantId());//商户号
        $input_obj->SetNonce_str(self::getNonceStr());//随机字符串

        $input_obj->SetSign($config);//签名
        $xml = $input_obj->ToXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $time_out);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 下载对账单，WxPayDownloadBill中bill_date为必填参数
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayConfigInterface $config 配置对象
     * @param WxPayDownloadBill    $input_obj
     * @param int                  $time_out
     *
     * @return 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public static function downloadBill($config, $input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/downloadbill";
        //检测必填参数
        if (!$input_obj->IsBill_dateSet()) {
            throw new WxPayException("对账单接口中，缺少必填参数bill_date！");
        }
        $input_obj->SetAppid($config->GetAppId());//公众账号ID
        $input_obj->SetMch_id($config->GetMerchantId());//商户号
        $input_obj->SetNonce_str(self::getNonceStr());//随机字符串

        $input_obj->SetSign($config);//签名
        $xml = $input_obj->ToXml();

        $response = self::postXmlCurl($config, $xml, $url, false, $time_out);
        if (substr($response, 0, 5) == "<xml>") {
            return "";
        }
        return $response;
    }

    /**
     * 提交被扫支付API
     * 收银员使用扫码设备读取微信用户刷卡授权码以后，二维码或条码信息传送至商户收银台，
     * 由商户收银台或者商户后台调用该接口发起支付。
     * WxPayWxPayMicroPay中body、out_trade_no、total_fee、auth_code参数必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayConfigInterface $config 配置对象
     * @param WxPayWxPayMicroPay   $input_obj
     * @param int                  $time_out
     */
    public static function micropay($config, $input_obj, $time_out = 10)
    {
        $url = "https://api.mch.weixin.qq.com/pay/micropay";
        //检测必填参数
        if (!$input_obj->IsBodySet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数body！");
        } else if (!$input_obj->IsOut_trade_noSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数out_trade_no！");
        } else if (!$input_obj->IsTotal_feeSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数total_fee！");
        } else if (!$input_obj->IsAuth_codeSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数auth_code！");
        }

        $input_obj->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip
        $input_obj->SetAppid($config->GetAppId());//公众账号ID
        $input_obj->SetMch_id($config->GetMerchantId());//商户号
        $input_obj->SetNonce_str(self::getNonceStr());//随机字符串

        $input_obj->SetSign($config);//签名
        $xml = $input_obj->ToXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $time_out);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 撤销订单API接口，WxPayReverse中参数out_trade_no和transaction_id必须填写一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayConfigInterface $config 配置对象
     * @param WxPayReverse         $input_obj
     * @param int                  $time_out
     *
     * @throws WxPayException
     */
    public static function reverse($config, $input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/reverse";
        //检测必填参数
        if (!$input_obj->IsOut_trade_noSet() && !$input_obj->IsTransaction_idSet()) {
            throw new WxPayException("撤销订单API接口中，参数out_trade_no和transaction_id必须填写一个！");
        }

        $input_obj->SetAppid($config->GetAppId());//公众账号ID
        $input_obj->SetMch_id($config->GetMerchantId());//商户号
        $input_obj->SetNonce_str(self::getNonceStr());//随机字符串

        $input_obj->SetSign($config);//签名
        $xml = $input_obj->ToXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, true, $time_out);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 测速上报，该方法内部封装在report中，使用时请注意异常流程
     * WxPayReport中interface_url、return_code、result_code、user_ip、execute_time_必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayConfigInterface $config 配置对象
     * @param WxPayReport          $input_obj
     * @param int                  $time_out
     *
     * @return 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public static function report($config, $input_obj, $time_out = 1)
    {
        $url = "https://api.mch.weixin.qq.com/payitil/report";
        //检测必填参数
        if (!$input_obj->IsInterface_urlSet()) {
            throw new WxPayException("接口URL，缺少必填参数interface_url！");
        }
        if (!$input_obj->IsReturn_codeSet()) {
            throw new WxPayException("返回状态码，缺少必填参数return_code！");
        }
        if (!$input_obj->IsResult_codeSet()) {
            throw new WxPayException("业务结果，缺少必填参数result_code！");
        }
        if (!$input_obj->IsUser_ipSet()) {
            throw new WxPayException("访问接口IP，缺少必填参数user_ip！");
        }
        if (!$input_obj->IsExecute_time_Set()) {
            throw new WxPayException("接口耗时，缺少必填参数execute_time_！");
        }
        $input_obj->SetAppid($config->GetAppId());//公众账号ID
        $input_obj->SetMch_id($config->GetMerchantId());//商户号
        $input_obj->SetUser_ip($_SERVER['REMOTE_ADDR']);//终端ip
        $input_obj->SetTime(date("YmdHis"));//商户上报时间
        $input_obj->SetNonce_str(self::getNonceStr());//随机字符串

        $input_obj->SetSign($config);//签名
        $xml = $input_obj->ToXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $time_out);
        return $response;
    }

    /**
     *
     * 生成二维码规则,模式一生成支付二维码
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayConfigInterface $config 配置对象
     * @param WxPayBizPayUrl       $input_obj
     * @param int                  $time_out
     *
     * @return 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public static function bizpayurl($config, $input_obj, $time_out = 6)
    {
        if (!$input_obj->IsProduct_idSet()) {
            throw new WxPayException("生成二维码，缺少必填参数product_id！");
        }

        $input_obj->SetAppid($config->GetAppId());//公众账号ID
        $input_obj->SetMch_id($config->GetMerchantId());//商户号
        $input_obj->SetTime_stamp(time());//时间戳
        $input_obj->SetNonce_str(self::getNonceStr());//随机字符串

        $input_obj->SetSign($config);//签名

        return $input_obj->GetValues();
    }

    /**
     *
     * 转换短链接
     * 该接口主要用于扫码原生支付模式一中的二维码链接转成短链接(weixin://wxpay/s/XXXXXX)，
     * 减小二维码数据量，提升扫描速度和精确度。
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayConfigInterface $config 配置对象
     * @param WxPayShortUrl        $input_obj
     * @param int                  $time_out
     *
     * @return 成功时返回，其他抛异常
     * @throws WxPayException
     */
    public static function shorturl($config, $input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/tools/shorturl";
        //检测必填参数
        if (!$input_obj->IsLong_urlSet()) {
            throw new WxPayException("需要转换的URL，签名用原串，传输需URL encode！");
        }
        $input_obj->SetAppid($config->GetAppId());//公众账号ID
        $input_obj->SetMch_id($config->GetMerchantId());//商户号
        $input_obj->SetNonce_str(self::getNonceStr());//随机字符串

        $input_obj->SetSign($config);//签名
        $xml = $input_obj->ToXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $time_out);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 支付结果通用通知
     *
     * @param function $callback
     * 直接回调函数使用方法: notify(you_function);
     * 回调类成员函数方法:notify(array($this, you_function));
     * $callback  原型为：function function_name($data){}
     */
    public static function notify($config, $callback, &$msg)
    {
        //获取通知的数据
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (empty($xml)) {
            # 如果没有数据，直接返回失败
            return false;
        }

        //如果返回成功则验证签名
        try {
            $result = WxPayNotifyResults::Init($config, $xml);
        } catch (WxPayException $e) {
            $msg = $e->errorMessage();
            return false;
        }

        return call_user_func($callback, $result);
    }

    /**
     *  产生随机字符串，不长于32位
     *
     * @param int $length
     *
     * @return string
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 直接输出xml
     *
     * @param string $xml
     */
    public static function replyNotify($xml)
    {
        echo $xml;
    }

    /**
     *
     * 上报数据， 上报的时候将屏蔽所有异常流程
     *
     * @param string $usrl
     * @param int    $startTimeStamp
     * @param array  $data
     */
    private static function reportCostTime($url, $startTimeStamp, $data)
    {
        //如果不需要上报数据
        $reportLevenl = $config->GetReportLevenl();
        if ($reportLevenl == 0) {
            return;
        }
        //如果仅失败上报
        if ($reportLevenl == 1 &&
            array_key_exists("return_code", $data) &&
            $data["return_code"] == "SUCCESS" &&
            array_key_exists("result_code", $data) &&
            $data["result_code"] == "SUCCESS") {
            return;
        }

        //上报逻辑
        $endTimeStamp = self::getMillisecond();
        $objInput = new WxPayReport();
        $objInput->SetInterface_url($url);
        $objInput->SetExecute_time_($endTimeStamp - $startTimeStamp);
        //返回状态码
        if (array_key_exists("return_code", $data)) {
            $objInput->SetReturn_code($data["return_code"]);
        }
        //返回信息
        if (array_key_exists("return_msg", $data)) {
            $objInput->SetReturn_msg($data["return_msg"]);
        }
        //业务结果
        if (array_key_exists("result_code", $data)) {
            $objInput->SetResult_code($data["result_code"]);
        }
        //错误代码
        if (array_key_exists("err_code", $data)) {
            $objInput->SetErr_code($data["err_code"]);
        }
        //错误代码描述
        if (array_key_exists("err_code_des", $data)) {
            $objInput->SetErr_code_des($data["err_code_des"]);
        }
        //商户订单号
        if (array_key_exists("out_trade_no", $data)) {
            $objInput->SetOut_trade_no($data["out_trade_no"]);
        }
        //设备号
        if (array_key_exists("device_info", $data)) {
            $objInput->SetDevice_info($data["device_info"]);
        }

        try {
            self::report($config, $objInput);
        } catch (WxPayException $e) {
            //不做任何处理
        }
    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml      请求内容xml格式
     * @param string $url      请求接口地址
     * @param bool   $use_cert 是否使用证书
     * @param int    $second   等待超时秒数
     *
     * @return bool|string
     * @throws WxPayException
     */
    private static function postXmlCurl($xml, $url, $use_cert = false, $second = 30)
    {
        $ch = curl_init();
        $curl_version = curl_version();
        $ua = "WXPaySDK/" . self::$VERSION . " (" . PHP_OS . ") PHP/" . PHP_VERSION . " CURL/" . $curl_version['version'] . " "
            . WxPayConfig::getInstance()->getMerchantId();

        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
        $curl_proxy = WxPayConfig::getInstance()->getCurlProxy();
        $proxy_host = $curl_proxy['curl_proxy_host'];
        $proxy_port = $curl_proxy['curl_proxy_port'];
        if ($proxy_host != "0.0.0.0" && $proxy_port != 0) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_host);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($use_cert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            //证书文件请放入服务器的非web目录下 ssl_cert_path, ssl_key_path
            $cert = WxPayConfig::getInstance()->getSSLCertPath();
            $ssl_cert_path = $cert['ssl_cert_path'];
            $ssl_key_path = $cert['ssl_key_path'];
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $ssl_cert_path);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $ssl_key_path);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new WxPayException("curl出错，错误码:$error");
        }
    }

    /**
     * 获取毫秒级别的时间戳
     *
     * @return array|string
     */
    private static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode(" ", microtime());
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2[0];
        return $time;
    }

    /**
     * 获取客户端IP地址
     *
     * @return string
     */
    private static function getClientIp()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches [0] : '';
    }

}