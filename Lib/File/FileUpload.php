<?php

namespace Lib\File;

use Lib\SPL\SplAbstract\ASingleBase;

/**
 * Author: skylong
 * CreateTime: 2018-8-11 16:45:32
 * Description: 文件上传类
 */
class FileUpload extends ASingleBase
{

    /**
     * 文件上传路径
     *
     * @var string
     */
    private $save_path;

    /**
     * 源文件名
     *
     * @var string
     */
    private $origin_name;

    /**
     * 临时文件名
     *
     * @var string
     */
    private $tmp_file_name;

    /**
     * 上传文件的类型
     *
     * @var string
     */
    private $file_type;

    /**
     * 上传文件的大小
     *
     * @var int
     */
    private $file_size;

    /**
     * 新文件名
     *
     * @var string
     */
    private $new_file_name;

    /**
     * 上传文件错误码
     *
     * @var int
     */
    private $error_num = 0;

    /**
     * 文件上传失败错误信息
     *
     * @var string
     */
    private $error_msg = '';

    /**
     * 允许上传的文件类型
     *
     * @var array
     */
    private $allow_type = ['jpg', 'gif', 'png', 'csv'];

    /**
     * 允许上传文件的大小（KB）
     *
     * @var int
     */
    private $upload_max_size = 5000;

    /**
     * 继承单例模式
     *
     * @return FileUpload|object
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * 设置上传文件大小
     *
     * @param int $size
     */
    public function setUploadMaxSize($size = 0)
    {
        $this->upload_max_size = ($size && is_numeric($size)) ? $size : $this->upload_max_size;
    }

    /**
     * 设置允许上传的文件类型
     *
     * @param array $allow_type
     */
    public function setAllowType($allow_type = [])
    {
        $this->allow_type = ($allow_type && is_array($allow_type)) ? $allow_type : $this->allow_type;
    }


    /**
     * 单个上传文件
     *
     * @param string $upload_field   上传文件的字段名（POST表单上传）
     * @param string $save_path      保存路径
     * @param string $save_file_name 保存文件名
     *
     * @return boolean
     */
    public function upload($upload_field, $save_path, $save_file_name = '')
    {
        if (!$this->checkFilePath($save_path)) {
            $this->error_msg = $this->errorMsg();
            return false;
        }

        //获取上传文件的信息
        $upload_info = $this->getUploadFileInfo($upload_field);
        if (!$upload_info) {
            return false;
        }

        //设置文件信息
        if (!$this->setFiles($upload_info['name'], $upload_info['tmp_name'], $upload_info['size'], $upload_info['error'])) {
            $this->error_msg = $this->errorMsg();
            return false;
        }

        //校验文件大小和文件类型
        if (!$this->checkFileSize() || !$this->checkFileType()) {
            $this->error_msg = $this->errorMsg();
            return false;
        }

        //设置上传后的文件名
        $this->setNewFileName($save_file_name);
        if (!$this->copyFile()) {
            $this->error_msg = $this->errorMsg();
            return false;
        }
        return true;
    }

    /**
     * 获取上传后的文件名
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->new_file_name;
    }

    /**
     * 获取错误信息
     *
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->error_msg;
    }

    /**
     * 获取上传文件的信息
     *
     * @param string $upload_field
     *
     * @return array|bool
     */
    private function getUploadFileInfo($upload_field)
    {
        if (!$upload_field || !isset($_FILES[$upload_field])) {
            $this->error_num = -6;
            return false;
        }
        $ret = [];
        $ret['name'] = $_FILES[$upload_field]['name'];
        $ret['tmp_name'] = $_FILES[$upload_field]['tmp_name'];
        $ret['size'] = $_FILES[$upload_field]['size'];
        $ret['error'] = $_FILES[$upload_field]['error'];
        return $ret;
    }

    /**
     * 错误信息配置
     *
     * @return string
     */
    private function errorMsg()
    {
        $str = "上传文件<font color='red'>{$this->origin_name}</font>时出错：";
        switch ($this->error_num) {
            case 7:
                $str .= "文件写入失败";
                break;
            case 6:
                $str .= "找不到临时文件夹";
                break;
            case 4:
                $str .= "没有文件被上传";
                break;
            case 3:
                $str .= "文件只有部分上传";
                break;
            case 2:
                $str .= "上传文件的大小超过了HTML表单中MAX_FILE_SIZE选项指定的值";
                break;
            case 1:
                $str .= "上传的文件超过了php.ini中upload_max_filesize选项限制的值";
                break;
            case -1:
                $str .= "未允许类型";
                break;
            case -2:
                $str .= '文件过大，上传的文件不能超过' . $this->upload_max_size . 'KB';
                break;
            case -3:
                $str .= "上传失败";
                break;
            case -4:
                $str .= "建立存放上传文件目录失败，请重新指定上传目录";
                break;
            case -5:
                $str .= "必须指定上传文件的路径";
                break;
            case -6:
                $str .= "没有找到上传文件的字段名";
                break;
            default :
                $str .= "未知错误或上传字段名错误";
        }
        return $str . '！';
    }

    /**
     * 设置上传后台的文件名
     *
     * @param string $save_file_name
     */
    private function setNewFileName($save_file_name = '')
    {
        if (!$save_file_name) {
            $this->new_file_name = $this->proRandName();
        } else {
            $this->new_file_name = $save_file_name . '.' . $this->file_type;
        }
    }

    /**
     * 文件路径校验
     *
     * @param string $save_path 保存文件路径
     *
     * @return boolean
     */
    private function checkFilePath($save_path = '')
    {
        if (empty($save_path)) {
            $this->error_num = -5;
            return false;
        }

        //校验文件路径是否存在，是否有写操作
        $this->save_path = rtrim(PATH_UPLOAD, DS) . DS . trim($save_path, DS) . DS;
        if (file_exists($this->save_path) && is_writable($this->save_path)) {
            return true;
        }

        //尝试创建文件目录
        if (!mkdir($this->save_path, 0755, true)) {
            $this->error_num = -4;
            return false;
        }
        return true;
    }

    /**
     * 检查文件大小(单位：KB)
     *
     * @return boolean
     */
    private function checkFileSize()
    {
        if (floor($this->file_size / 1024) > $this->upload_max_size) {
            $this->error_num = -2;
            return false;
        }
        return true;
    }

    /**
     * 检查文件类型
     *
     * @return boolean
     */
    private function checkFileType()
    {
        if (!in_array(strtolower($this->file_type), $this->allow_type)) {
            $this->error_num = -1;
            return false;
        }
        return true;
    }

    /**
     * 设置文件信息
     *
     * @param string $name     源文件名
     * @param string $tmp_name 临时文件名
     * @param int    $size     文件大小
     * @param int    $error    文件上传错误码
     *
     * @return boolean
     */
    private function setFiles($name = '', $tmp_name = '', $size = 0, $error = 0)
    {
        $this->error_num = $error;
        if ($error) {
            return false;
        }
        $this->origin_name = $name;
        $this->tmp_file_name = $tmp_name;
        $aryStr = explode('.', $name);
        $this->file_type = strtolower($aryStr[count($aryStr) - 1]);
        $this->file_size = $size;
        return true;
    }

    /**
     * 随机文件名
     *
     * @return string
     */
    private function proRandName()
    {
        $fileName = date('YmdHis') . '_' . rand(100, 999);
        return $fileName . '.' . $this->file_type;
    }

    /**
     * 复制临时文件到指定目录
     *
     * @return boolean
     */
    private function copyFile()
    {
        if ($this->error_num) {
            return false;
        }
        $save_file_name = $this->save_path . $this->new_file_name;
        if (!(\move_uploaded_file($this->tmp_file_name, $save_file_name))) {
            $this->error_num = -3;
            return false;
        }
        return true;
    }

}
