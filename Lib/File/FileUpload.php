<?php

namespace Lib\File;

defined('IN_APP') or die('Access denied!');

use Config\ConfigUpload;

/**
 * Author: skylong
 * CreateTime: 2018-8-11 16:45:32
 * Description: 文件上传类
 */
class FileUpload {

    private $origin_name;
    private $tmp_file_name;
    private $file_type;
    private $file_size;
    private $new_file_name;
    private $error_num = 0;
    private $error_msg = '';

    public function upload($upload_files, $save_path, $save_file_name) {
        $return = true;
        if (!$this->checkFilePath($save_path)) {
            $this->error_msg = $this->errorMsg();
            return false;
        }
        $name     = $_FILES[$fileField]['name'];
        $tmp_name = $_FILES[$fileField]['tmp_name'];
        $size     = $_FILES[$fileField]['size'];
        $error    = $_FILES[$fileField]['error'];
        if (is_array($name)) {
            $error = array();
            for ($i = 0; $i < count($name); $i++) {
                if ($this->setFiles($name[$i], $tmp_name[$i], $size[$i], $error[$i])) {
                    if (!$this->checkFileSize() || !$this->checkFileType()) {
                        $error[] = $this->errorMsg();
                        $return  = false;
                    }
                } else {
                    $error[] = $this->errorMsg();
                    $return  = false;
                }
                if (!$return) {
                    $this->setFiles();
                }
            }
            if ($return) {
                $fileNames = array();
                for ($i = 0; $i < count($name); $i++) {
                    if ($this->setFiles($name[$i], $tmp_name[$i], $size[$i], $error[$i])) {
                        $this->setNewFileName();
                        if (!$this->copyFile()) {
                            $error[] = $this->errorMsg();
                            $return  = false;
                        }
                        $fileNames[] = $this->newFileName;
                    }
                }
                $this->newFileName = $fileNames;
            }
            $this->error_msg = $error;
            return $return;
        } else {
            if ($this->setFiles($name, $tmp_name, $size, $error)) {
                if ($this->checkFileSize() && $this->checkFileType()) {
                    $this->setNewFileName();
                    if ($this->copyFile()) {
                        return true;
                    } else {
                        $return = false;
                    }
                } else {
                    $return = false;
                }
            } else {
                $return = false;
            }
            if (!$return) {
                $this->error_msg = $this->errorMsg();
            }
            return $return;
        }
    }

    /**
     * 获取上传后的文件名
     * 
     * @return string
     */
    public function getFileName() {
        return $this->new_file_name;
    }

    /**
     * 读取错误信息
     * 
     * @return string
     */
    public function getErrorMsg() {
        return $this->error_msg;
    }

    /**
     * 错误信息配置
     * 
     * @return string
     */
    private function errorMsg() {
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
                $str .= '文件过大，上传的文件不能超过' . ConfigUpload::UPLOAD_MAX_SIZE . 'KB';
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
            default :
                $str .= "未知错误";
        }
        return $str . '！';
    }

    public function setNewFileName() {
        if ($this->israndname) {
            $this->setOption('newFileName', $this->proRandName());
        } else {
            $this->setOption('newFileName', $this->originName);
        }
    }

    /**
     * 文件路径校验
     * 
     * @param string $save_path
     * @return boolean
     */
    public function checkFilePath($save_path = '') {
        if (empty($save_path)) {
            $this->error_num = -5;
            return false;
        }
        $save_path = rtrim(UPLOAD_PATH, DS) . DS . trim($save_path, DS);
        if (file_exists($save_path) && is_writable($save_path)) {
            return true;
        }
        if (!mkdir($save_path, 0755, true)) {
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
    public function checkFileSize() {
        if (floor($this->file_size / 1024) > ConfigUpload::UPLOAD_MAX_SIZE) {
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
    public function checkFileType() {
        if (!in_array(strtolower($this->file_type), ConfigUpload::$allow_type)) {
            $this->error_num = -1;
            return false;
        }
        return true;
    }

    public function setFiles($name = '', $tmp_name = '', $size = 0, $error = 0) {
        $this->error_num = $error;
        if ($error) {
            return false;
        }
        $this->setOption('originName', $name);
        $this->setOption('tmpFileName', $tmp_name);
        $aryStr = explode('.', $name);
        $this->setOption('fileType', strtolower($aryStr[count($aryStr) - 1]));
        $this->setOption('fileSize', $size);
        return true;
    }

    public function proRandName() {
        $fileName = date('YmdHis') . '_' . rand(100, 999);
        return $fileName . '.' . $this->fileType;
    }

    public function copyFile() {
        if ($this->error_num) {
            return false;
        }
        $path = rtrim($this->path, '/') . '/';
        $path .= $this->newFileName;
        if (\move_uploaded_file($this->tmpFileName, $path)) {
            return true;
        } else {
            $this->setOption('errorNum', -3);
            return false;
        }
    }

}
