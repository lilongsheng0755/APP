<?php

namespace Lib\WxPay;

use Lib\SPL\SplAbstract\ASingleBase;

/**
 * Author: lilongsheng
 * CreateTime: 2019/9/2 11:07
 * Description:微信支付配置相关定义类
 */
class WxPayConfig extends ASingleBase
{
    /**
     * 付款码支付
     */
    const PAY_TYPE_PAYCODE = 1;

    /**
     * Native支付
     */
    const PAY_TYPE_NATIVE = 2;

    /**
     * JSAPI支付
     */
    const PAY_TYPE_JSAPI = 3;

    /**
     * APP支付
     */
    const PAY_TYPE_APP = 4;

    /**
     * H5支付
     */
    const PAY_TYPE_H5 = 5;

    /**
     * 小程序支付
     */
    const PAY_TYPE_SMALL_PROCEDURE = 6;

    /**
     * 保存配置参数
     *
     * @var array
     */
    private $config = [];

    /**
     * 继承单例模式
     *
     * @return object|WxPayConfig
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * 设置签名生成算法类型 支持md5和sha256方式
     *
     * @param string $sign_type MD5|HMAC-SHA256
     **/
    public function setSignType($sign_type)
    {
        $this->config['sign_type'] = $sign_type;
    }

    /**
     * 获取签名生成算法类型
     *
     * @return mixed
     * @throws WxPayException
     */
    public function getSignType()
    {
        if (!isset($this->config['sign_type']) || !$this->config['sign_type']) {
            throw new WxPayException('未设置签名类型！');
        }

        return $this->config['sign_type'];
    }


    /**
     * 设置商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
     *
     * @param string $pay_key
     */
    public function setPayKey($pay_key = '')
    {
        $this->config['pay_key'] = $pay_key;
    }

    /**
     * 获取商户支付密钥
     *
     * @return mixed
     * @throws WxPayException
     */
    public function getPayKey()
    {
        if (!isset($this->config['pay_key']) || !$this->config['pay_key']) {
            throw new WxPayException('未配置商户支付密钥！');
        }
        return $this->config['pay_key'];
    }

    /**
     * 设置绑定支付的APPID（必须配置，开户邮件中可查看）
     *
     * @param string $appid
     */
    public function setAppId($appid = '')
    {
        $this->config['appid'] = $appid;
    }

    /**
     * 获取绑定支付的APPID
     *
     * @return mixed
     * @throws WxPayException
     */
    public function getAppId()
    {
        if (!isset($this->config['appid']) || !$this->config['appid']) {
            throw new WxPayException('未配置绑定支付的APPID！');
        }
        return $this->config['appid'];
    }

    /**
     * 设置公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）
     *
     * @param string $secret_key
     */
    public function setAppSecret($secret_key = '')
    {
        $this->config['secret_key'] = $secret_key;
    }

    /**
     * 获取公众帐号secert
     *
     * @return mixed
     * @throws WxPayException
     */
    public function getAppSecret()
    {
        if (!isset($this->config['secret_key']) || !$this->config['secret_key']) {
            throw new WxPayException('未配置公众帐号secert！');
        }
        return $this->config['secret_key'];
    }


    /**
     * 设置商户号（必须配置，开户邮件中可查看）
     *
     * @param string $mch_id
     */
    public function setMerchantId($mch_id = '')
    {
        $this->config['mch_id'] = $mch_id;
    }

    /**
     * 获取配置的商户号
     *
     * @return mixed
     * @throws WxPayException
     */
    public function getMerchantId()
    {
        if (!isset($this->config['mch_id']) || !$this->config['mch_id']) {
            throw new WxPayException('未配置商户号！');
        }
        return $this->config['mch_id'];
    }

    /**
     * 设置支付回调URL地址
     *
     * @param string $notify_url 仅支持单个URL地址，不能带get请求参数
     */
    public function setNotifyUrl($notify_url = '')
    {
        $this->config['notify_url'] = $notify_url;
    }

    /**
     * 获取支付的回调URL地址
     *
     * @return mixed
     * @throws WxPayException
     */
    public function getNotifyUrl()
    {
        if (!isset($this->config['notify_url']) || !$this->config['notify_url']) {
            throw new WxPayException('未配置支付回调地址！');
        }
        return $this->config['notify_url'];
    }

    /**
     * 设置curl代理服务器【POST方法】
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     *
     * @param string $proxy_host 代理IP地址
     * @param string $proxy_port 代理端口
     */
    public function setCurlProxy($proxy_host = '', $proxy_port = '')
    {
        $this->config['curl_proxy_host'] = $proxy_host ? $proxy_host : '0.0.0.0';
        $this->config['curl_proxy_port'] = $proxy_port ? $proxy_port : '0';
    }

    /**
     * 获取curl代理服务器IP和地址
     *
     * @return array ['curl_proxy_host' => '0.0.0.0','curl_proxy_port' => '0']
     */
    public function getCurlProxy()
    {
        $ret = [
            'curl_proxy_host' => '0.0.0.0',
            'curl_proxy_port' => '0',
        ];
        if (isset($this->config['curl_proxy_host']) && $this->config['curl_proxy_host'] != '0.0.0.0') {
            $ret['curl_proxy_host'] = $this->config['curl_proxy_host'];
        }
        if (isset($this->config['curl_proxy_port']) && $this->config['curl_proxy_port'] != '0') {
            $ret['curl_proxy_port'] = $this->config['curl_proxy_port'];
        }
        return $ret;
    }

    /**
     * 设置上报等级
     * 接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】
     *
     * @param int $report_level 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     */
    public function setReportLevel($report_level = 1)
    {
        $this->config['report_level'] = $report_level;
    }

    /**
     * 设置上报等级
     *
     * @return int|mixed
     */
    public function getReportLevel()
    {
        return isset($this->config['report_level']) ? $this->config['report_level'] : 1;
    }

    /**
     * 设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * 注意:
     * 1.证书文件不能放在web服务器虚拟目录，应放在有访问权限控制的目录中，防止被他人下载；
     * 2.建议将证书文件名改为复杂且不容易猜测的文件名；
     * 3.商户服务器要做好病毒和木马防护工作，不被非法侵入者窃取证书文件。
     *
     * @param string $ssl_cert_path 证书cert文件路径
     * @param string $ssl_key_path  证书key文件路径
     */
    public function setSSLCertPath($ssl_cert_path = '', $ssl_key_path = '')
    {
        $this->config['ssl_cert_path'] = $ssl_cert_path ? $ssl_cert_path : '';
        $this->config['ssl_key_path'] = $ssl_key_path ? $ssl_key_path : '';
    }

    /**
     * 获取商户证书路
     *
     * @return array ['ssl_cert_path' => '', 'ssl_key_path' => '']
     * @throws WxPayException
     */
    public function getSSLCertPath()
    {
        $ret = ['ssl_cert_path' => '', 'ssl_key_path' => ''];
        if (isset($this->config['ssl_cert_path']) || !$this->config['ssl_cert_path']) {
            throw new WxPayException('未配置商户证书cert文件路径！');
        }
        if (isset($this->config['ssl_key_path']) || !$this->config['ssl_key_path']) {
            throw new WxPayException('未配置商户证书key文件路径！');
        }
        return $ret;
    }

}