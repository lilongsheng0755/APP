<?php

//不使用缓存
header("Cache-Control: no-cache");
header("Pragma: no-cache");

//设置返回编码
//header('Content-Type: text/html;charset=utf-8');
//载入核心文件
require_once __DIR__ . '/core.php';

$error = error_get_last();
$db = new Lib\DB\SMysqli('127.0.0.1', 'test', 'test', 'test');

//读取json文件数据并写入数据库
for ($i = 20; $i <= 27; $i++) {
    $fh = fopen("./export/majiang_game_record2018-11-{$i}.json", 'r');
    while ($row = fgets($fh)) {
        $row = json_decode(trim($row), true);
        if (!$row) {
            continue;
        }
        $insert_data = array(
            'id'         => $row['id'],
            'uid'        => $row['uid'],
            'roomid'     => $row['roomid'],
            'num'        => $row['num'],
            'player1'    => $row['player1'],
            'player2'    => $row['player2'],
            'player3'    => $row['player3'],
            'player4'    => $row['player4'],
            'data_game'  => $row['data_game'],
            'date'       => $row['date'],
            'type'       => $row['type'],
            'master'     => $row['master'],
            'pay'        => $row['pay'],
            'club_id'    => $row['club_id'],
            'clubmid'    => $row['clubmid'],
            'colltype'   => $row['colltype'],
            'fixed_type' => $row['fixed_type'],
        );
        $str = '';
        foreach ($insert_data as $key => $val) {
            $str .= "{$key}='{$val}',";
        }
        $str = rtrim($str, ',');
        $sql = "INSERT INTO majiang_game_record SET $str";
        $db->query($sql);
    }
    fclose($fh);
}

