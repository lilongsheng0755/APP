<?php

namespace Lib\Request;

use Lib\System\Log;
use Config\ConfigLog;

/**
 * Author: skylong
 * CreateTime: 2018-12-1 11:13:04
 * Description: 基于curl扩展辅助类
 */
class Curl
{

    /**
     * POST方式请求
     */
    const METHOD_POST = 'POST';

    /**
     * GET方式请求
     */
    const METHOD_GET = 'GET';

    /**
     * 数据为超级文本类型传输
     */
    const HEADER_HTML = 'html';

    /**
     * 数据为json类型传输
     */
    const HEADER_JSON = 'json';

    /**
     * CURL等待超时秒数
     */
    const CURL_CONNECTTIMEOUT = 3;

    /**
     * 执行curl请求
     *
     * @param string $url         url请求地址
     * @param array  $data        请求参数
     * @param string $method      请求方式 HEADER_*
     * @param string $header_type 请求头信息类型 EADER_*
     *
     * @return boolean
     */
    public static function curlExec($url, $data = [], $method = self::METHOD_POST, $header_type = self::HEADER_JSON)
    {
        if ((!$url = trim($url))) {
            return false;
        }
        $ch = curl_init($url);
        $request = '';
        if ($data && is_array($data)) {
            $request = self::doRequestParams($data, $header_type);
        }
        //是否为post请求
        if ($method == self::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, 1);
            $request && curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        } else {
            $parse_url = parse_url($url);
            $url = (strpos($url, '?') !== false && !empty($parse_url['query'])) ? "{$url}&{$request}" : rtrim($url, '?') . "?{$request}";
            $request && curl_setopt($ch, CURLOPT_URL, $url);
        }
        curl_setopt($ch, CURLOPT_HEADER, false); // 启用时会将头文件的信息作为数据流输出。 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //将curl_exec()获取的信息以字符串返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CURL_CONNECTTIMEOUT); //等待超时秒数

        if (strpos($url, 'https://') !== false) {  //绕过ssl验证
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $header = self::getHeaderType($header_type);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //curl设置header头
        }
        $ret = curl_exec($ch); //成功时返回 TRUE ， 或者在失败时返回 FALSE 。 然而，如果 CURLOPT_RETURNTRANSFER 选项被设置，函数执行成功时会返回执行的结果
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        $info = curl_getinfo($ch);
        if ($error || $errno || ($info && isset($info['http_code']) && $info['http_code'] >= 400)) {
            $errno = $errno ? $errno : $info['http_code'];
            $error = $error ? $error : 'http_code ' . $errno;
            self::writeErrLog($errno, $error);
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return $ret;
    }

    /**
     * 获取请求头信息设置
     * POST请求并且enctype属性没有设置的情况下有效
     * 通过输入端来获取数据 file_get_contents('php://input');
     * Content-Type：发送的数据类型
     * Accept：希望接受的数据类型
     *
     * @param string $header_type
     *
     * @return array
     */
    private static function getHeaderType($header_type = '')
    {
        if (!$header_type) {
            return [];
        }
        $header = [];
        switch ($header_type) {
            case self::HEADER_JSON:
                array_push($header, 'Content-Type:application/json');
                array_push($header, 'Accept:application/json');
                break;
            case self::HEADER_HTML :
                array_push($header, 'Content-Type:text/html;');
                array_push($header, 'Accept:text/html,application/xhtml+xml,application/xml');
                break;
            default:
                return [];
        }
        return $header;
    }

    /**
     * 处理请求参数的格式
     *
     * @param array  $request     请求的数据
     * @param string $header_type 请求头信息类型 EADER_*
     *
     * @return string
     */
    private static function doRequestParams($request = [], $header_type = self::HEADER_JSON)
    {
        if ($header_type == self::HEADER_JSON) {
            return $request ? json_encode($request) : '';
        }
        $params = '';
        foreach ($request as $key => $val) {
            $params .= "{$key}={$val}&";
        }
        return $params ? rtrim(str_replace([' ', "\n", "\t", "\r"], '', $params), '&') : '';
    }

    /**
     * 写操作curl失败的日志
     *
     * @param int $errno 错误编号
     * @param int $error 错误信息
     */
    private static function writeErrLog($errno, $error)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 0);
        $trace && $trace = array_pop($trace);
        $err_file = (string)$trace['file'] . '(' . (string)$trace['line'] . ')';
        !PRODUCTION_ENV && die($err_file . '=======CURL Err：' . $error);
        $data = "file:{$err_file}" . PHP_EOL;
        $data .= "time:" . date('Y-m-d H:i:s') . PHP_EOL;
        $data .= "errno:{$errno}" . PHP_EOL;
        $data .= "error:{$error}" . PHP_EOL;
        $data .= "======================================================================" . PHP_EOL;
        Log::writeErrLog('error_curl' . date('Ymd'), $data, ConfigLog::ERR_CURL_LOG_TYPE);
    }

}
