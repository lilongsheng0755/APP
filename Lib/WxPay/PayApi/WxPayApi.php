<?php

namespace Lib\WxPay\PayApi;

use Lib\WxPay\PayConfig\WxPayConfig;
use Lib\WxPay\PayData\WxPayBizPayUrl;
use Lib\WxPay\PayData\WxPayCloseOrder;
use Lib\WxPay\PayData\WxPayDownloadBill;
use Lib\WxPay\PayData\WxPayMicroPay;
use Lib\WxPay\PayData\WxPayOrderQuery;
use Lib\WxPay\PayData\WxPayRefund;
use Lib\WxPay\PayData\WxPayRefundQuery;
use Lib\WxPay\PayData\WxPayReport;
use Lib\WxPay\PayData\WxPayReverse;
use Lib\WxPay\PayData\WxPayShortUrl;
use Lib\WxPay\PayData\WxPayUnifiedOrder;
use Lib\WxPay\PayException\WxPayException;
use Lib\WxPay\PayResults\WxPayNotify;
use Lib\WxPay\PayResults\WxPayResults;

/**
 * Author: skylong
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
     * @param int               $time_out  请求等待秒数
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
        $input_obj->setSign($input_obj->makeSign());
        $xml = $input_obj->toXml();

        $start_time_stamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $time_out);
        $result = WxPayResults::init($response);
        self::reportCostTime($url, $start_time_stamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 查询订单
     *
     * @param WxPayOrderQuery $input_obj 查询订单对象
     * @param int             $time_out  请求等待秒数
     *
     * @return array|bool
     * @throws WxPayException
     */
    public static function orderQuery($input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //检测必填参数
        if (!$input_obj->isOutTradeNoSet() && !$input_obj->isTransactionIdSet()) {
            throw new WxPayException("订单查询接口中，out_trade_no、transaction_id至少填一个！");
        }
        $input_obj->setAppid(WxPayConfig::getInstance()->getAppId());//公众账号ID
        $input_obj->setMchId(WxPayConfig::getInstance()->getMerchantId());//商户号
        $input_obj->setNonceStr(self::getNonceStr());//随机字符串

        //生成签名
        $input_obj->setSign($input_obj->makeSign());
        $xml = $input_obj->toXml();

        $start_time_stamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $time_out);
        $result = WxPayResults::init($response);
        self::reportCostTime($url, $start_time_stamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 关闭订单
     *
     * @param WxPayCloseOrder $input_obj 关闭订单对象
     * @param int             $time_out  请求等待秒数
     *
     * @return array|bool
     * @throws WxPayException
     */
    public static function closeOrder($input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/closeorder";
        //检测必填参数
        if (!$input_obj->isOutTradeNoSet()) {
            throw new WxPayException("订单查询接口中，out_trade_no必填！");
        }
        $input_obj->setAppid(WxPayConfig::getInstance()->getAppId());//公众账号ID
        $input_obj->setMchId(WxPayConfig::getInstance()->getMerchantId());//商户号
        $input_obj->setNonceStr(self::getNonceStr());//随机字符串

        //生成签名
        $input_obj->setSign($input_obj->makeSign());
        $xml = $input_obj->toXml();

        $start_time_stamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $time_out);
        $result = WxPayResults::init($response);
        self::reportCostTime($url, $start_time_stamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 申请退款
     *
     * @param WxPayRefund $input_obj 申请退款对象
     * @param int         $time_out  请求等待秒数
     *
     * @return array|bool
     * @throws WxPayException
     */
    public static function refund($input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        //检测必填参数
        if (!$input_obj->isOutTradeNoSet() && !$input_obj->isTransactionIdSet()) {
            throw new WxPayException("退款申请接口中，out_trade_no、transaction_id至少填一个！");
        } else if (!$input_obj->isOutRefundNoSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数out_refund_no！");
        } else if (!$input_obj->isTotalFeeSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数total_fee！");
        } else if (!$input_obj->isRefundFeeSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数refund_fee！");
        } else if (!$input_obj->isOpUserIdSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数op_user_id！");
        }

        $input_obj->setAppid(WxPayConfig::getInstance()->getAppId());//公众账号ID
        $input_obj->setMchId(WxPayConfig::getInstance()->getMerchantId());//商户号
        $input_obj->setNonceStr(self::getNonceStr());//随机字符串

        //生成签名
        $input_obj->setSign($input_obj->makeSign());
        $xml = $input_obj->toXml();

        $start_time_stamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, true, $time_out);
        $result = WxPayResults::init($response);
        self::reportCostTime($url, $start_time_stamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 查询退款
     *
     * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
     * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
     *
     * @param WxPayRefundQuery $input_obj 查询退款对象
     * @param int              $time_out  请求等待秒数
     *
     * @return array|bool
     * @throws WxPayException
     */
    public static function refundQuery($input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/refundquery";
        //检测必填参数
        if (!$input_obj->isOutRefundNoSet() &&
            !$input_obj->isOutTradeNoSet() &&
            !$input_obj->isTransactionIdSet() &&
            !$input_obj->isRefundIdSet()) {
            throw new WxPayException("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！");
        }

        $input_obj->setAppid(WxPayConfig::getInstance()->getAppId());//公众账号ID
        $input_obj->setMchId(WxPayConfig::getInstance()->getMerchantId());//商户号
        $input_obj->setNonceStr(self::getNonceStr());//随机字符串

        //生成签名
        $input_obj->setSign($input_obj->makeSign());
        $xml = $input_obj->toXml();

        $start_time_stamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $time_out);
        $result = WxPayResults::init($response);
        self::reportCostTime($url, $start_time_stamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 下载对账单
     *
     * @param WxPayDownloadBill $input_obj 下载对账单对象
     * @param int               $time_out  请求等待秒数
     *
     * @return bool|string
     * @throws WxPayException
     */
    public static function downloadBill($input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/downloadbill";
        //检测必填参数
        if (!$input_obj->isBillDateSet()) {
            throw new WxPayException("对账单接口中，缺少必填参数bill_date！");
        }
        $input_obj->setAppid(WxPayConfig::getInstance()->getAppId());//公众账号ID
        $input_obj->setMchId(WxPayConfig::getInstance()->getMerchantId());//商户号
        $input_obj->setNonceStr(self::getNonceStr());//随机字符串

        //生成签名
        $input_obj->setSign($input_obj->makeSign());
        $xml = $input_obj->toXml();

        $response = self::postXmlCurl($xml, $url, false, $time_out);
        if (substr($response, 0, 5) == "<xml>") {
            return "";
        }
        return $response;
    }

    /**
     * 被扫支付【用户选择付款码支付并打开微信，进入“我”->“钱包”->“收付款”条码界面】
     *
     * 收银员使用扫码设备读取微信用户刷卡授权码以后，二维码或条码信息传送至商户收银台，
     * 由商户收银台或者商户后台调用该接口发起支付。
     *
     * @param WxPayMicroPay $input_obj 被扫支付对象
     * @param int           $time_out  请求等待秒数
     *
     * @return array|bool
     * @throws WxPayException
     */
    public static function micropay($input_obj, $time_out = 10)
    {
        $url = "https://api.mch.weixin.qq.com/pay/micropay";
        //检测必填参数
        if (!$input_obj->isBodySet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数body！");
        } else if (!$input_obj->isOutTradeNoSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数out_trade_no！");
        } else if (!$input_obj->isTotalFeeSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数total_fee！");
        } else if (!$input_obj->isAuthCodeSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数auth_code！");
        }

        $input_obj->setSpbillCreateIp(self::getClientIp());//终端ip
        $input_obj->setAppid(WxPayConfig::getInstance()->getAppId());//公众账号ID
        $input_obj->setMchId(WxPayConfig::getInstance()->getMerchantId());//商户号
        $input_obj->setNonceStr(self::getNonceStr());//随机字符串

        //生成签名
        $input_obj->setSign($input_obj->makeSign());
        $xml = $input_obj->toXml();

        $start_time_stamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $time_out);
        $result = WxPayResults::init($response);
        self::reportCostTime($url, $start_time_stamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 撤销订单
     *
     * @param WxPayReverse $input_obj 撤销订单对象
     * @param int          $time_out  请求等待秒数
     *
     * @return array|bool
     * @throws WxPayException
     */
    public static function reverse($input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/reverse";
        //检测必填参数
        if (!$input_obj->isOutTradeNoSet() && !$input_obj->isTransactionIdSet()) {
            throw new WxPayException("撤销订单API接口中，参数out_trade_no和transaction_id必须填写一个！");
        }

        $input_obj->setAppid(WxPayConfig::getInstance()->getAppId());//公众账号ID
        $input_obj->setMchId(WxPayConfig::getInstance()->getMerchantId());//商户号
        $input_obj->setNonceStr(self::getNonceStr());//随机字符串

        //生成签名
        $input_obj->setSign($input_obj->makeSign());
        $xml = $input_obj->toXml();

        $start_time_stamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, true, $time_out);
        $result = WxPayResults::init($response);
        self::reportCostTime($url, $start_time_stamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 测速上报，该方法内部封装在report中，使用时请注意异常流程
     *
     * @param WxPayReport $input_obj 测速上报对象
     * @param int         $time_out  请求等待秒数
     *
     * @return bool|string
     * @throws WxPayException
     */
    public static function report($input_obj, $time_out = 1)
    {
        $url = "https://api.mch.weixin.qq.com/payitil/report";
        //检测必填参数
        if (!$input_obj->isInterfaceUrlSet()) {
            throw new WxPayException("接口URL，缺少必填参数interface_url！");
        }
        if (!$input_obj->isReturnCodeSet()) {
            throw new WxPayException("返回状态码，缺少必填参数return_code！");
        }
        if (!$input_obj->isResultCodeSet()) {
            throw new WxPayException("业务结果，缺少必填参数result_code！");
        }
        if (!$input_obj->isUserIpSet()) {
            throw new WxPayException("访问接口IP，缺少必填参数user_ip！");
        }
        if (!$input_obj->isExecuteTimeSet()) {
            throw new WxPayException("接口耗时，缺少必填参数execute_time_！");
        }
        $input_obj->setAppid(WxPayConfig::getInstance()->getAppId());//公众账号ID
        $input_obj->setMchId(WxPayConfig::getInstance()->getMerchantId());//商户号
        $input_obj->setUserIp(self::getClientIp());//终端ip
        $input_obj->setTime(date("YmdHis"));//商户上报时间
        $input_obj->setNonceStr(self::getNonceStr());//随机字符串

        //生成签名
        $input_obj->setSign($input_obj->makeSign());
        $xml = $input_obj->toXml();
        $response = self::postXmlCurl($xml, $url, false, $time_out);
        return $response;
    }

    /**
     * 生成二维码规则,模式一生成支付二维码
     *
     * @param WxPayBizPayUrl|WxPayUnifiedOrder $input_obj 生成支付二维码对象
     * @param int                              $time_out  请求等待秒数
     *
     * @return mixed
     * @throws WxPayException
     */
    public static function bizpayurl($input_obj, $time_out = 6)
    {
        if (!$input_obj->isProductIdSet()) {
            throw new WxPayException("生成二维码，缺少必填参数product_id！");
        }

        if (!$input_obj->isTradeTypeSet()) {
            throw new WxPayException("生成二维码，缺少必填参数trade_type！");
        }

        if ($input_obj->getTradeType() != 'NATIVE') {
            throw new WxPayException("生成二维码，trade_type类型不是NATIVE！");
        }

        return self::unifiedOrder($input_obj, $time_out);
    }

    /**
     * 转换短链接
     *
     * 该接口主要用于扫码原生支付模式一中的二维码链接转成短链接(weixin://wxpay/s/XXXXXX)，
     * 减小二维码数据量，提升扫描速度和精确度。
     *
     * @param WxPayShortUrl $input_obj 转换短链接对象
     * @param int           $time_out  请求等待秒数
     *
     * @return array|bool
     * @throws WxPayException
     */
    public static function shorturl($input_obj, $time_out = 6)
    {
        $url = "https://api.mch.weixin.qq.com/tools/shorturl";
        //检测必填参数
        if (!$input_obj->isLongUrlSet()) {
            throw new WxPayException("需要转换的URL，签名用原串，传输需URL encode！");
        }
        $input_obj->setAppid(WxPayConfig::getInstance()->getAppId());//公众账号ID
        $input_obj->setMchId(WxPayConfig::getInstance()->getMerchantId());//商户号
        $input_obj->setNonceStr(self::getNonceStr());//随机字符串

        //生成签名
        $input_obj->setSign($input_obj->makeSign());
        $xml = $input_obj->toXml();
        $input_obj->setLongUrl(urlencode($input_obj->getLongUrl()));

        $start_time_stamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $time_out);
        $result = WxPayResults::init($response);
        self::reportCostTime($url, $start_time_stamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 支付结果通用通知
     *
     * @param string|array $callback 回调方法
     * @param string       $msg
     *
     * @return bool|mixed
     */
    public static function notify($callback, &$msg)
    {
        //获取通知的数据
        $xml = file_get_contents("php://input");
        if (empty($xml)) {
            # 如果没有数据，直接返回失败
            return false;
        }

        //如果返回成功则验证签名
        try {
            $result = WxPayNotify::init($xml);
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
     * 上报数据， 上报的时候将屏蔽所有异常流程
     *
     * @param string $url              上报url地址
     * @param string $start_time_stamp 调用支付接口开始时间
     * @param array  $data             调用支付接口返回的数据
     *
     * @return bool
     */
    private static function reportCostTime($url, $start_time_stamp, $data)
    {
        //如果不需要上报数据
        $reportLevenl = WxPayConfig::getInstance()->getReportLevel();
        if ($reportLevenl == 0) {
            return true;
        }
        //如果仅失败上报
        if ($reportLevenl == 1 &&
            array_key_exists("return_code", $data) &&
            $data["return_code"] == "SUCCESS" &&
            array_key_exists("result_code", $data) &&
            $data["result_code"] == "SUCCESS") {
            return true;
        }

        //上报逻辑
        $endTimeStamp = self::getMillisecond();
        $objInput = WxPayReport::getInstance();
        $objInput->setInterfaceUrl($url);
        $objInput->setExecuteTime($endTimeStamp - $start_time_stamp);
        //返回状态码
        if (array_key_exists("return_code", $data)) {
            $objInput->setReturnCode($data["return_code"]);
        }
        //返回信息
        if (array_key_exists("return_msg", $data)) {
            $objInput->setReturnMsg($data["return_msg"]);
        }
        //业务结果
        if (array_key_exists("result_code", $data)) {
            $objInput->setResultCode($data["result_code"]);
        }
        //错误代码
        if (array_key_exists("err_code", $data)) {
            $objInput->setErrCode($data["err_code"]);
        }
        //错误代码描述
        if (array_key_exists("err_code_des", $data)) {
            $objInput->setErrCodeDes($data["err_code_des"]);
        }
        //商户订单号
        if (array_key_exists("out_trade_no", $data)) {
            $objInput->setOutTradeNo($data["out_trade_no"]);
        }
        //设备号
        if (array_key_exists("device_info", $data)) {
            $objInput->setDeviceInfo($data["device_info"]);
        }

        try {
            return self::report($objInput);
        } catch (WxPayException $e) {
            return false;
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