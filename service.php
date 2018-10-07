<?php

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//设置返回编码
header('Content-Type: text/html;charset=utf-8');
//载入核心文件
require_once 'core.php';

function show(...$key) {
    input(...$key);
}

function input(...$key) {
    var_dump(func_get_args());
}

show('mykey',100,2000);
