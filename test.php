<?php
/**
 * Author: skylong
 * CreateTime: 2019/9/10 13:45
 * Description:
 */
//载入核心文件
require_once __DIR__ . '/core.php';

$server_udp = new \Thread\ServerUDP('192.168.1.24',50001);
$server_udp->setRedisConfig('127.0.0.1');
$server_udp->createServer();