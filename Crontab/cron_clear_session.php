<?php

//载入核心文件
require_once '../core.php';

/**
 * Author: skylong
 * CreateTime: 2018-6-12 16:57:06
 * Description: 每天凌晨两点，定时清理过期session数据【定时任务由后台同一管理】
 */
if (!IS_CLI) {
    die(1001);
}

Swoole\Timer::tick(1000, function ($timer_id) {
    file_put_contents('test.log', date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
});
