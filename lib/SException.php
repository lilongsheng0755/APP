<?php

namespace lib;

/**
 * Author: skylong
 * CreateTime: 2018-6-7 16:10:57
 * Description: 自定义异常类
 */
class SException extends \Exception {

    /**
     * 文件不存在时异常码
     */
    const CODE_NOT_FOUND_FILE = -1;

    /**
     * MYSQL异常类型
     */
    const CODE_MYSQL_ERROR    = -2;

    /**
     * MongoDB异常类型
     */
    const CODE_MONGODB_ERROR  = -3;

    /**
     * 覆盖Exception类构造方法
     */
    public function __construct($message = "", $code = 0) {
        parent::__construct($message, $code);
    }

    /**
     * 覆盖Exception类的__toString方法
     * 
     * @return string
     */
    public function __toString() {
        if (APP_DEBUG) {
            $data = array('file' => $this->file, 'line' => $this->line, 'msg' => $this->message);
            return $this->debugView($data);
        } else {
            $data = array();
        }
        return json_encode(array('code' => $this->code, 'data' => $data));
    }

    /**
     * debug模板
     * 
     * @param array $data
     * @return string
     */
    private function debugView($data) {
        $html = '<div style="width:50%; position:fixed; right:0px; bottom:0px; border:1px solid #3cff0d; padding:10px; background:#ccc; color:red;">';
        foreach ($data as $th => $msg) {
            $html .= '<p style="text-decoration:underline;"><span style="font-weight:bold;">' . $th . '：</span><span>' . $msg . '</span></p>';
        }
        $html .= '</div>';
        return $html;
    }

}
