<?php

namespace helper;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:24:38
 * Description: 图片处理辅助类
 */
class HelperImage {

    /**
     * 图片随Y轴翻转
     * 
     * @param type $filename
     */
    public static function trun_y($filename) {
        $back   = imagecreatefromjpeg($filename);
        $width  = imagesx($back);
        $height = imagesy($back);

        $new = imagecreatetruecolor($width, $height);
        for ($x = 0; $x < $width; $x++) {
            imagecopy($new, $back, $width - $x - 1, 0, $x, 0, 1, $height);
        }
        imagejpeg($new, $filename);
        imagedestroy($back);
        imagedestroy($new);
    }

    /**
     * 图片随X轴翻转
     * 
     * 
     * @param type $filename
     */
    public static function trun_x($filename) {
        $back   = imagecreatefromjpeg($filename);
        $width  = imagesx($back);
        $height = imagesy($back);

        $new = imagecreatetruecolor($width, $height);
        for ($y = 0; $y < $height; $y++) {
            imagecopy($new, $back, 0, $height - $y - 1, 0, $y, $width, 1);
        }
        imagejpeg($new, $filename);
        imagedestroy($back);
        imagedestroy($new);
    }

}
