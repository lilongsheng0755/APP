<?php

namespace lib;

/**
 * Author: skylong
 * CreateTime: 2018-6-7 16:10:57
 * Description: 自定义异常类
 */
class SException extends \Exception {

    //文件不存在时异常码
    const CODE_NOT_FOUND_FILE = -1;

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
        return json_encode(array('code' => $this->code, 'data' => array()));
    }

}
