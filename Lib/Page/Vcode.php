<?php

namespace Lib\Page;

/**
 * Author: skylong
 * CreateTime: 2018-8-12 12:13:14
 * Description: 字符验证码类
 */
class Vcode
{

    private $width;
    private $height;
    private $codeNum;
    private $disturbColorNum;
    private $checkCode;
    private $image;

    /**
     * 初始化验证码
     *
     * @param int $width   验证码图片宽度
     * @param int $height  验证码图片高度
     * @param int $codeNum 验证码图片字符个数
     */
    public function __construct($width = 80, $height = 20, $codeNum = 4)
    {
        $this->width = $width;
        $this->height = $height;
        $this->codeNum = $codeNum;
        $number = floor(($height * $width) / 15);
        if ($number > 240 - $codeNum) {
            $this->disturbColorNum = 240 - $codeNum;
        } else {
            $this->disturbColorNum = $number;
        }
        $this->checkCode = $this->createCheckCode();
    }

    /**
     * 输出验证码图片
     */
    public function outImg()
    {
        $this->getCreateImage();
        $this->setDisturbColor();
        $this->outputText();
        $this->outputImage();
        $this->vcodeToSession();
    }

    /**
     * 画一个矩形
     */
    private function getCreateImage()
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $backColor = imagecolorallocate($this->image, rand(225, 255), rand(225, 255), rand(225, 255));
        imagefill($this->image, 0, 0, $backColor);
        $border = imagecolorallocate($this->image, 0, 0, 0);
        imagerectangle($this->image, 0, 0, $this->width - 1, $this->height - 1, $border);
    }

    /**
     * 设置验证码字符串
     *
     * @return string
     */
    private function createCheckCode()
    {
        $rand_code = '3456789abcdefghijkmnpqrstuvwxyABCDEFGHIJKMNPQRSTUVWXY';
        $code = '';
        for ($i = 0; $i < $this->codeNum; $i++) {
            $code .= $rand_code[rand(0, strlen($rand_code) - 1)];
        }
        return $code;
    }

    /**
     * 画像素点，线条
     */
    private function setDisturbColor()
    {
        for ($i = 0; $i < $this->disturbColorNum; $i++) {
            $color = imagecolorallocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255));
            imagesetpixel($this->image, rand(1, $this->width - 2), rand(1, $this->height - 2), $color);
        }

        for ($i = 0; $i < 10; $i++) {
            $color = imagecolorallocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255));
            imagearc($this->image, rand(-10, $this->width), rand(-10, $this->height), rand(30, 300), rand(20, 200), 55, 44, $color);
        }
    }

    /**
     * 画验证码字符
     */
    private function outputText()
    {
        for ($i = 0; $i < $this->codeNum; $i++) {
            $fontcolor = imagecolorallocate($this->image, rand(0, 128), rand(0, 128), rand(0, 128));
            $fontsize = rand(3, 5);
            $x = floor($this->width / $this->codeNum) * $i + 3;
            $y = rand(0, $this->height - imagefontheight($fontsize));
            imagechar($this->image, $fontsize, $x, $y, $this->checkCode[$i], $fontcolor);
        }
    }

    /**
     * 生成图片
     */
    private function outputImage()
    {
        if (imagetypes() & IMG_GIF) {
            header('Content-Type:image/gif');
            imagegif($this->image);
        } elseif (imagetypes() & IMG_JPG) {
            header('Content-Type:image/jpeg');
            imagejpeg($this->image);
        } elseif (imagetypes() & IMG_PNG) {
            header('Content-Type:image/png');
            imagepng($this->image);
        } elseif (imagetypes() & IMG_WBMP) {
            header('Content-Type:image/vnd.wap.wbmp');
            imagewbmp($this->image);
        } else {
            die('PHP不支持图像创建！');
        }
    }

    /**
     * 验证码写入session
     *
     * @return string
     */
    public function vcodeToSession()
    {
        $_SESSION['vcode'] = strtolower($this->checkCode);
        return $_SESSION['vcode'];
    }

    /**
     * 销毁图片资源
     */
    public function __destruct()
    {
        if (is_object($this->image)) {
            imagedestroy($this->image);
        }
    }

}
