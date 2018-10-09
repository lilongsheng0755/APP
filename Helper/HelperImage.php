<?php

namespace Helper;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:24:38
 * Description: 图片处理辅助类
 */
class HelperImage {

    /**
     * 图片随Y轴翻转
     * 原理：从右侧一个像素条一个像素条，从左到右的方式拷贝到新的一个画布
     * 
     * @param string $filename  文件名
     */
    public static function trunY($filename) {
        $back   = imagecreatefromjpeg($filename);
        $width  = imagesx($back);
        $height = imagesy($back);

        $new = imagecreatetruecolor($width, $height);
        for ($x = 0; $x < $width; $x++) {
            imagecopy($new, $back, $width - $x - 1, 0, $x, 0, 1, $height);
        }
        $flag = imagejpeg($new, $filename);
        imagedestroy($back);
        imagedestroy($new);
        return $flag;
    }

    /**
     * 图片随X轴翻转
     * 原理：从下一个像素条一个像素条，从上到下的方式拷贝到新的一个画布
     * 
     * @param string $filename  文件名
     */
    public static function trunX($filename) {
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

    //缩放图片
    public static function thumb($name, $width, $height, $qz = 'th_') {
        $imgInfo = self::getInfo($name);
        $srcImg  = self::getImg($name, $imgInfo);
        $size    = self::getNewSize($name, $width, $height, $imgInfo);
        $newImg  = self::kidOfImage($srcImg, $size, $imgInfo);
        return self::createNewImage($newImg, $qz . $name, $imgInfo);
    }

    //加水印
    public static function waterMark($groundName, $waterName, $waterPos = 0, $qz = 'wa_') {
        $curpath = UPLOAD_PATH . '/';
        $dir     = dirname($waterName);
        if ($dir == '.') {
            $wpath = $curpath;
        } else {
            $wpath     = $dir . '/';
            $waterName = basename($waterName);
        }
        if (file_exists($curpath . $groundName) && file_exists($wpath . $waterName)) {
            $groundInfo = self::getInfo($groundName);
            $waterInfo  = self::getInfo($waterName, $dir);
            if (!$pos        = self::position($groundInfo, $waterInfo, $waterPos)) {
                echo '背景不应该比水印图片小！';
                return false;
            }
            $groundImg = self::getImg($groundName, $groundInfo);
            $waterImg  = self::getImg($waterName, $waterInfo, $dir);

            $groundImg = self::copyImage($groundImg, $waterImg, $pos, $waterInfo);
            return self::createNewImage($groundImg, $qz . $groundName, $groundInfo);
        } else {
            echo '图片或水印图片不存在！';
            return false;
        }
    }

    //图片剪切
    public static function cut($name, $x, $y, $width, $height, $qz = 'cu_') {
        $imgInfo = self::getInfo($name);
        if ((($x + $width) > $imgInfo['width']) || (($y + $height) > $imgInfo['height'])) {
            echo '裁剪的位置超出了背景图片范围';
            return false;
        }

        $back = self::getImg($name, $imgInfo);

        $cutimg = imagecreatetruecolor($width, $height);
        imagecopyresampled($cutimg, $back, 0, 0, $x, $y, $width, $height, $width, $height);
        imagedestroy($back);
        return self::createNewImage($cutimg, $qz . $name, $imgInfo);
    }

    //确定水印图片的位置
    private static function position($groundInfo, $waterInfo, $waterPos) {
        if ($groundInfo['width'] < $waterInfo['width'] || $groundInfo['height'] < $waterInfo['height']) {
            return false;
        }
        switch ($waterPos) {
            case 1: //顶端居左
                $posX = 0;
                $posY = 0;
                break;
            case 2: //顶端水平居中
                $posX = ($groundInfo['width'] - $waterInfo['width']) / 2;
                $posY = 0;
                break;
            case 3: //顶端居右
                $posX = $groundInfo['width'] - $waterInfo['width'];
                $posY = 0;
                break;
            case 4: //靠左垂直居中
                $posX = 0;
                $posY = ($groundInfo['height'] - $waterInfo['height']) / 2;
                break;
            case 5: //水平和垂直居中
                $posX = ($groundInfo['width'] - $waterInfo['width']) / 2;
                $posY = ($groundInfo['height'] - $waterInfo['height']) / 2;
                break;
            case 6: //靠右垂直居中
                $posX = $groundInfo['width'] - $waterInfo['width'];
                $posY = ($groundInfo['height'] - $waterInfo['height']) / 2;
                break;
            case 7: //底部靠左
                $posX = 0;
                $posY = $groundInfo['height'] - $waterInfo['height'];
                break;
            case 8: //底部水平居中
                $posX = ($groundInfo['width'] - $waterInfo['width']) / 2;
                $posY = $groundInfo['height'] - $waterInfo['height'];
                break;
            case 9: //底部靠右
                $posX = $groundInfo['width'] - $waterInfo['width'];
                $posY = $groundInfo['height'] - $waterInfo['height'];
                break;
            default : //随机
                $posX = rand(0, ($groundInfo['width'] - $waterInfo['width']));
                $posY = rand(0, ($groundInfo['height'] - $waterInfo['height']));
        }
        return array('posX' => $posX, 'posY' => $posY);
    }

    //获取图片信息
    private static function getInfo($name, $path = '.') {
        $spath = $path == '.' ? rtrim(UPLOAD_PATH, '/') . '/' : $path . '/';

        $data              = getimagesize($spath . $name);
        $imgInfo['width']  = $data[0];
        $imgInfo['height'] = $data[1];
        $imgInfo['type']   = $data[2];
        return $imgInfo;
    }

    //获取图片画布
    private static function getImg($name, $imgInfo, $path = '.') {
        $spath = $path == '.' ? rtrim(UPLOAD_PATH, '/') . '/' : $path . '/';

        $srcPic = $spath . $name;
        switch ($imgInfo['type']) {
            case 1:
                $img = imagecreatefromgif($srcPic);
                break;
            case 2:
                $img = imagecreatefromjpeg($srcPic);
                break;
            case 3:
                $img = imagecreatefrompng($srcPic);
                break;
            default :
                return false;
        }
        return $img;
    }

    //返回等比缩放图片的宽度和高度，如果原图比缩放后的还小保持不变
    private static function getNewSize($name, $width, $height, $imgInfo) {
        $size['width']  = $imgInfo['width'];
        $size['height'] = $imgInfo['height'];

        if ($width < $imgInfo['width']) {
            $size['width'] = $width;
        }
        if ($height < $imgInfo['height']) {
            $size['height'] = $height;
        }
        //等比缩放算法
        if ($imgInfo['width'] * $size['width'] > $imgInfo['height'] * $size['height']) {
            $size['height'] = round($imgInfo['height'] * $size['width'] / $imgInfo['width']);
        } else {
            $size['width'] = round($imgInfo['width'] * $size['height'] / $imgInfo['height']);
        }
        return $size;
    }

    //用于保存图像，并保留原有图片格式
    private static function createNewImage($newImg, $newName, $imgInfo) {
        switch ($imgInfo['type']) {
            case 1:
                $result = imagegif($newImg, UPLOAD_PATH . $newName);
                break;
            case 2:
                $result = imagejpeg($newImg, UPLOAD_PATH . $newName);
                break;
            case 3:
                $result = imagepng($newImg, UPLOAD_PATH . $newName);
                break;
            default :
                return false;
        }
        imagedestroy($newImg);
        return $newName;
    }

    //用于加水印时复制图像
    private static function copyImage($groundImg, $waterImg, $pos, $waterInfo) {
        imagecopy($groundImg, $waterImg, $pos['posX'], $pos['posY'], 0, 0, $waterInfo['width'], $waterInfo['height']);
        imagedestroy($waterImg);
        return $groundImg;
    }

    //处理带有透明度的图片保存原样
    private static function kidOfImage($srcImg, $size, $imgInfo) {
        $newImg = imagecreatetruecolor($size['width'], $size['height']);
        $otsc   = imagecolortransparent($srcImg);
        if ($otsc >= 0 && $otsc < imagecolorstotal($srcImg)) {
            $transparentcolor    = imagecolorsforindex($srcImg, $otsc);
            $newtransparentcolor = imagecolorallocate($newImg, $transparentcolor['red'], $transparentcolor['green'], $transparentcolor['blue']);
            imagefill($newImg, 0, 0, $newtransparentcolor);
            imagecolortransparent($newImg, $newtransparentcolor);
        }
        imagecopyresized($newImg, $srcImg, 0, 0, 0, 0, $size['width'], $size['height'], $imgInfo['width'], $imgInfo['height']);
        imagedestroy($srcImg);
        return $newImg;
    }

}
