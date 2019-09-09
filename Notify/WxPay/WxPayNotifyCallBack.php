<?php

namespace Notify\WxPay;

use Lib\WxPay\PayData\WxPayOrderQuery;
use Lib\WxPay\PayResults\WxPayNotify;

/**
 * Author: lilongsheng
 * CreateTime: 2019/9/9 14:54
 * Description: 微信支付通知发货
 */
class WxPayNotifyCallBack extends WxPayNotify
{

    public function __construct()
    {

    }

    /**
     * 查询订单
     *
     * @param $transaction_id
     *
     * @return bool
     * @throws WxPayException
     */
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $config = new WxPayConfig(H5_PAY_CONFIG_ID);

        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($config, $input);

        if (isset($result['return_code']) && isset($result['result_code']) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
            return true;
        }
        return false;
    }

    /**
     * 重写回调处理函数
     *
     * @param array|WxPayNotifyResults $data
     * @param string                   $msg
     *
     * @return bool|true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($data, &$msg)
    {
        //回调数据上报
        if ($data instanceof WxPayNotifyResults) {
            $data = $data->GetValues();
        }

        //回调数据上报
        $param['l_type'] = 'preturn';
        $param['l_ip'] = functions::getip();
        $param['return_type'] = 'wxpay';
        $aError = array_merge($param, $data);
        $flag = false;

        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            $aError['return_msg'] = $msg;
            oo::error()->writeErrorLog($aError);
            return false;
        }

        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            $aError['return_msg'] = $msg;
            oo::error()->writeErrorLog($aError);
            return false;
        }
        //发货流程：只有根据商户订单号找到该条信息
        $orderInfo = payment::getOne($data['out_trade_no']);
        //处理一笔订单支付两笔的问题
        $orderInfo = payment::doRepeatOrder($orderInfo, $data["transaction_id"], $data['out_trade_no']);
        if ($orderInfo['pstatus'] == 2) { //重复发货
            $msg = "重复发货";
            $aError['return_msg'] = $msg;
            oo::error()->writeErrorLog($aError);
            return true;
        }
        if ($orderInfo['isagentmall'] == 0) {
            //这里出过BUG，整型与浮点型不能比较
            if ($data['total_fee'] != intval($orderInfo['pamount'] * 100)) {
                $msg = "金额不对";
                $aError['return_msg'] = $msg;
                oo::error()->writeErrorLog($aError);
                return false;
            }
            $flag = payment::complectPayment($orderInfo['pid'], 2);
            if ($flag === true) { //发货
                payment::updateTranno($orderInfo['pid'], $data['transaction_id'], $data['out_trade_no']);
                $aError['return_msg'] = '发货成功';
                oo::error()->writeErrorLog($aError);
                return true;
            }
        } elseif ($orderInfo['isagentmall'] == 1 || $orderInfo['isagentmall'] == 2) {
            //这里出过BUG，整型与浮点型不能比较
            if ($data['total_fee'] != intval($orderInfo['discount'] * 100)) {
                $msg = "金额不对";
                $aError['return_msg'] = $msg;
                oo::error()->writeErrorLog($aError);
                return false;
            }
            $flag = payment::complectPayment($orderInfo['pid'], 2, 1, 0, 1);
            if ($flag === true) { //发货
                payment::updateTranno($orderInfo['pid'], $data['transaction_id'], $data['out_trade_no']);
                $aError['return_msg'] = '发货成功';
                oo::error()->writeErrorLog($aError);
                return true;
            }
        }
        $aError['return_msg'] = $flag == false ? '发货失败' : $flag;
        oo::error()->writeErrorLog($aError);
        return true;
    }

}

$notify = new PayNotifyCallBack();
$config = new WxPayConfig(H5_PAY_CONFIG_ID);
$notify->Handle($config, false);