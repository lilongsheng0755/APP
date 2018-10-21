<?php

namespace Lib\File;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-10-21 21:02:50
 * Description: 图片处理类（加水印，缩放图片，剪切）
 */
class Image {

    /**
     * 内部实例
     *
     * @var Image
     */
    private static $instance = null;

    /**
     * 错误信息
     *
     * @var string
     */
    private $error_msg = '';

    /**
     * 禁用外部实例化
     * 
     * @return boolean
     */
    private function __construct() {
        return false;
    }

    /**
     * 内部实例化
     * 
     * @return Image
     */
    public static function getInstance() {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        self::$instance = new self;
        return self::$instance;
    }

    /**
     * 获取错误信息
     * 
     * @return string
     */
    public function getErrorMsg() {
        return $this->error_msg;
    }

    /**
     * 缩放图片
     * 
     * @param string $img_file  原图路径
     * @param int $width 缩略图宽度
     * @param int $height 缩略图高度
     * @param string $save_path 缩略图保存路径
     * @return boolean
     */
    public function thumb($img_file, $width, $height, $save_path = '') {
        if (!file_exists($img_file)) {
            $this->error_msg = "图片不存在！";
            return false;
        }
        $save_path = $save_path ? $save_path : dirname($img_file);
        if (!$this->checkSavePath($save_path)) {
            $this->error_msg = "保存目录不存在或不可写！";
            return false;
        }
        $img_info  = $this->getInfo($img_file);
        $src_img   = $this->getImg($img_file, $img_info);
        $size      = $this->getNewSize($width, $height, $img_info);
        $new_img   = $this->kidOfImage($src_img, $size, $img_info);
        $save_file = rtrim($save_path, DS) . DS . 'th_' . basename($img_file);
        return $this->createNewImage($new_img, $save_file, $img_info);
    }

    /**
     * 图片加水印
     * 
     * @param string $ground_file 需要加水印的图片
     * @param string $water_file 水印图片
     * @param int $postion 加水印的位置：水印的位置：默认随机，1-顶端居左，2-顶端水平居中，3-顶端居右，4-靠左垂直居中，
     * 5-水平和垂直居中，6-靠右垂直居中，7-底部靠左，8-底部水平居中，9-底部靠右
     * @param string $save_path 加完水印后的图片保存路径
     * @return boolean
     */
    public function waterMark($ground_file, $water_file, $postion = 0, $save_path = '') {
        if (!file_exists($ground_file)) {
            $this->error_msg = "图片不存在！";
            return false;
        }
        if (!file_exists($water_file)) {
            $this->error_msg = "水印图片不存在！";
            return false;
        }
        $save_path = $save_path ? $save_path : dirname($ground_file);
        if (!$this->checkSavePath($save_path)) {
            $this->error_msg = "保存目录不存在或不可写！";
            return false;
        }
        $ground_info = $this->getInfo($ground_file);
        $water_info  = $this->getInfo($water_file);
        $water_pos   = $this->position($ground_info, $water_info, $postion);
        if ($ground_info['width'] < $water_info['width'] || $ground_info['height'] < $water_info['height']) {
            $this->error_msg = "背景不应该比水印图片小！";
            return false;
        }
        $ground_img = $this->getImg($ground_file, $ground_info);
        $water_img  = $this->getImg($water_file, $water_info);

        $new_img   = $this->copyImage($ground_img, $water_img, $water_pos, $water_info);
        $save_file = rtrim($save_path, DS) . DS . 'wa_' . basename($ground_file);
        return $this->createNewImage($new_img, $save_file, $ground_info);
    }

    /**
     * 图片剪切
     * 
     * @param string $img_file 原图片
     * @param int $x 开始坐标X
     * @param int $y 开始坐标Y
     * @param int $width 剪切宽度
     * @param int $height 剪切高度
     * @param string $save_path 保存路径
     * @return boolean
     */
    public function cut($img_file, $x, $y, $width, $height, $save_path = '') {
        if (!file_exists($img_file)) {
            $this->error_msg = "图片不存在！";
            return false;
        }
        $save_path = $save_path ? $save_path : dirname($img_file);
        if (!$this->checkSavePath($save_path)) {
            $this->error_msg = "保存目录不存在或不可写！";
            return false;
        }
        $img_info = $this->getInfo($img_file);
        if ((($x + $width) > $img_info['width']) || (($y + $height) > $img_info['height'])) {
            $this->error_msg = "裁剪的位置超出了背景图片范围！";
            return false;
        }
        $back      = $this->getImg($img_file, $img_info);
        $cutimg    = imagecreatetruecolor($width, $height);
        imagecopyresampled($cutimg, $back, 0, 0, $x, $y, $width, $height, $width, $height);
        imagedestroy($back);
        $save_file = rtrim($save_path, DS) . DS . 'cu_' . basename($img_file);
        return $this->createNewImage($cutimg, $save_file, $img_info);
    }

    /**
     * 图片随Y轴翻转
     * 原理：从右侧一个像素条一个像素条，从左到右的方式拷贝到新的一个画布
     * 
     * @param string $img_file  图片文件
     */
    public function trunY($img_file) {
        if (!file_exists($img_file)) {
            $this->error_msg = "图片不存在！";
            return false;
        }
        $back   = imagecreatefromjpeg($img_file);
        $width  = imagesx($back);
        $height = imagesy($back);

        $new = imagecreatetruecolor($width, $height);
        for ($x = 0; $x < $width; $x++) {
            imagecopy($new, $back, $width - $x - 1, 0, $x, 0, 1, $height);
        }
        $flag = imagejpeg($new, $img_file);
        imagedestroy($back);
        imagedestroy($new);
        return $flag;
    }

    /**
     * 图片随X轴翻转
     * 原理：从下一个像素条一个像素条，从上到下的方式拷贝到新的一个画布
     * 
     * @param string $img_file  图片文件
     */
    public function trunX($img_file) {
        if (!file_exists($img_file)) {
            $this->error_msg = "图片不存在！";
            return false;
        }
        $back   = imagecreatefromjpeg($img_file);
        $width  = imagesx($back);
        $height = imagesy($back);

        $new = imagecreatetruecolor($width, $height);
        for ($y = 0; $y < $height; $y++) {
            imagecopy($new, $back, 0, $height - $y - 1, 0, $y, $width, 1);
        }
        $flag = imagejpeg($new, $img_file);
        imagedestroy($back);
        imagedestroy($new);
        return $flag;
    }

    /**
     * 获取图片大小，类型等
     * 
     * @param string $img_file 图片地址
     * @return array
     */
    private function getInfo($img_file = '') {
        $data               = getimagesize($img_file);
        $img_info['width']  = $data[0];
        $img_info['height'] = $data[1];
        $img_info['type']   = $data[2];
        return $img_info;
    }

    /**
     * 根据图片尺寸获取图片画布
     * 
     * @param string $img_file  图片地址
     * @param array $img_info  图片信息
     * @return mixed
     */
    private function getImg($img_file, $img_info) {
        switch ($img_info['type']) {
            case 1:
                $img = imagecreatefromgif($img_file);
                break;
            case 2:
                $img = imagecreatefromjpeg($img_file);
                break;
            case 3:
                $img = imagecreatefrompng($img_file);
                break;
            default :
                return false;
        }
        return $img;
    }

    /**
     * 确定水印图片的位置
     * 
     * @param array $ground_info 需要加水印的图片信息
     * @param array $water_info 水印图片信息
     * @param int $water_pos 水印的位置：默认随机，1-顶端居左，2-顶端水平居中，3-顶端居右，4-靠左垂直居中，
     * 5-水平和垂直居中，6-靠右垂直居中，7-底部靠左，8-底部水平居中，9-底部靠右
     * @return boolean
     */
    private function position($ground_info, $water_info, $water_pos) {
        switch ($water_pos) {
            case 1: //顶端居左
                $posX = 0;
                $posY = 0;
                break;
            case 2: //顶端水平居中
                $posX = ($ground_info['width'] - $water_info['width']) / 2;
                $posY = 0;
                break;
            case 3: //顶端居右
                $posX = $ground_info['width'] - $water_info['width'];
                $posY = 0;
                break;
            case 4: //靠左垂直居中
                $posX = 0;
                $posY = ($ground_info['height'] - $water_info['height']) / 2;
                break;
            case 5: //水平和垂直居中
                $posX = ($ground_info['width'] - $water_info['width']) / 2;
                $posY = ($ground_info['height'] - $water_info['height']) / 2;
                break;
            case 6: //靠右垂直居中
                $posX = $ground_info['width'] - $water_info['width'];
                $posY = ($ground_info['height'] - $water_info['height']) / 2;
                break;
            case 7: //底部靠左
                $posX = 0;
                $posY = $ground_info['height'] - $water_info['height'];
                break;
            case 8: //底部水平居中
                $posX = ($ground_info['width'] - $water_info['width']) / 2;
                $posY = $ground_info['height'] - $water_info['height'];
                break;
            case 9: //底部靠右
                $posX = $ground_info['width'] - $water_info['width'];
                $posY = $ground_info['height'] - $water_info['height'];
                break;
            default : //随机
                $posX = rand(0, ($ground_info['width'] - $water_info['width']));
                $posY = rand(0, ($ground_info['height'] - $water_info['height']));
        }
        return array('posX' => $posX, 'posY' => $posY);
    }

    /**
     * 返回等比缩放图片的宽度和高度，如果原图比缩放后的还小保持不变
     * 
     * @param int $width 缩略图宽度
     * @param int $height 缩略图高度
     * @param array $img_info 原图信息
     * @return array
     */
    private function getNewSize($width, $height, $img_info) {
        $size['width']  = $img_info['width'];
        $size['height'] = $img_info['height'];

        if ($width < $img_info['width']) {
            $size['width'] = $width;
        }
        if ($height < $img_info['height']) {
            $size['height'] = $height;
        }
        //等比缩放算法
        if ($img_info['width'] * $size['width'] > $img_info['height'] * $size['height']) {
            $size['height'] = round($img_info['height'] * $size['width'] / $img_info['width']);
        } else {
            $size['width'] = round($img_info['width'] * $size['height'] / $img_info['height']);
        }
        return $size;
    }

    /**
     * 校验目录是否存在，是否有可写权限
     * 
     * @param string $save_path 保存路径
     * @return boolean
     */
    private function checkSavePath($save_path = '') {
        if (file_exists($save_path) && is_dir($save_path) && is_writable($save_path)) {
            return true;
        }
        return mkdir($save_path, 0744, true);
    }

    /**
     * 用于保存图像，并保留原有图片格式
     * 
     * @param source $new_img 缩略图资源
     * @param string $save_file 保存文件
     * @param array $img_info 原图片信息
     * @return boolean
     */
    private function createNewImage($new_img, $save_file, $img_info) {
        switch ($img_info['type']) {
            case 1:
                $result = imagegif($new_img, $save_file);
                break;
            case 2:
                $result = imagejpeg($new_img, $save_file);
                break;
            case 3:
                $result = imagepng($new_img, $save_file);
                break;
            default :
                return false;
        }
        imagedestroy($new_img);
        return $result;
    }

    /**
     * 处理带有透明度的图片保存原样
     * 
     * @param source $src_img 原图片资源
     * @param array $size 缩略图大小
     * @param array $img_info  原图片信息
     * @return source 缩略图资源
     */
    private static function kidOfImage($src_img, $size, $img_info) {
        $new_img = imagecreatetruecolor($size['width'], $size['height']);
        $otsc    = imagecolortransparent($src_img);
        if ($otsc >= 0 && $otsc < imagecolorstotal($src_img)) {
            $transparentcolor    = imagecolorsforindex($src_img, $otsc);
            $newtransparentcolor = imagecolorallocate($new_img, $transparentcolor['red'], $transparentcolor['green'], $transparentcolor['blue']);
            imagefill($new_img, 0, 0, $newtransparentcolor);
            imagecolortransparent($new_img, $newtransparentcolor);
        }
        imagecopyresized($new_img, $src_img, 0, 0, 0, 0, $size['width'], $size['height'], $img_info['width'], $img_info['height']);
        imagedestroy($src_img);
        return $new_img;
    }

    /**
     * 用于加水印时复制图像
     * 
     * @param source $ground_img 需要加水印的图片资源
     * @param source $water_img 水印图片资源
     * @param array $water_pos 水印的位置
     * @param array $water_info 水印图片信息
     * @return source
     */
    private static function copyImage($ground_img, $water_img, $water_pos, $water_info) {
        imagecopy($ground_img, $water_img, $water_pos['posX'], $water_pos['posY'], 0, 0, $water_info['width'], $water_info['height']);
        imagedestroy($water_img);
        return $ground_img;
    }

}
