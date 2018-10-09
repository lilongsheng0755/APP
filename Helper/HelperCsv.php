<?php

namespace Helper;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-8-6 16:30:17
 * Description: csv文件的导入导出
 */
class HelperCsv {

    /**
     * 导出csv文件
     * 
     * @param array $list  数据列表
     * @param array $title 数据列标题
     * @param int $limit 导出数据条数限制
     * @param string $file_name  自定义文件名
     */
    public static function exportCsv($list = array(), $title = array(), $limit = 1000, $file_name = '') {
        ob_clean();
        $file_name = $file_name . '_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $file_name);
        header('Cache-Control: max-age=0');
        $file      = fopen('php://output', 'a');
        $calc      = 0;
        foreach ($title as $v) {
            $tit[] = iconv('UTF-8', 'GB2312//IGNORE', $v);
        }
        fputcsv($file, $tit);
        foreach ($list as $v) {
            if ($limit == $calc) {
                break;
            }
            foreach ($v as $t) {
                $tarr[] = iconv('UTF-8', 'GB2312//IGNORE', $t);
            }
            fputcsv($file, $tarr);
            unset($tarr);
            $calc++;
        }
        unset($list);
        fclose($file);
        exit();
    }

    /**
     * csv导入，文件上传先保存到服务器upload目录下
     * 然后读取文件信息展示到页面进行二次确认
     * 
     * 
     * @param string $csv_file csv文件
     * @return array
     */
    public static function importCsv($csv_file) {
        $res    = array();
        $n      = 0;
        $handle = fopen($csv_file, 'r');
        while ($data   = fgetcsv($handle)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $res[$n][$i] = iconv('gb2312', 'utf-8', $data[$i]);
            }
            $n++;
        }
        fclose($handle);
        return $res;
    }

}
