<?php

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//载入核心文件
require_once __DIR__ . '/core.php';

//$pdo = new Lib\DB\SPDO('192.168.0.101', 'root', 'root', 'gamecard_log');
//for ($a = 0; $a <= 255; $a++) {
//    for ($b = 0; $b <= 255; $b++) {
//        for ($c = 0; $c <= 255; $c++) {
//            for ($d = 0; $d <= 255; $d++) {
//                $ip = $a . '.' . $b . '.' . $c . '.' . $d;
//                $ip2long = sprintf('%u', ip2long($ip));
//                if ($ip2long > 2147483647) {
//                    $sql = "insert into `test` set `ip`='{$ip}',`ip2long`='{$ip2long}'";
//                    $pdo->exec($sql);
//                }     
//            }
//        }
//    }
//}
//
//echo 'IP生成完毕';
/*
$mem = new Lib\Cache\SMemcached([['192.168.3.182', 11211, 100]]);
$mem->set('mykey', 'he he da', 120);

var_dump($mem->get('mykey'), $mem->touch('mykey', 120));*/

$image = \Lib\File\Image::getInstance();

var_dump($image);
