<?php

//载入核心文件
require_once '../core.php';

/**
 * Author: skylong
 * CreateTime: 2018-6-12 16:57:06
 * Description: 每天凌晨两点，定时清理过期session数据
 */
if (!IS_CLI) {
    die(1001);
}


