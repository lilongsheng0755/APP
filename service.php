<?php

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//设置返回编码
//header('Content-Type: text/html;charset=utf-8');
//载入核心文件
require_once __DIR__ . '/core.php';

$flag = Lib\File\Image::getInstance()->trunY('./test.jpg');
var_dump($flag);
