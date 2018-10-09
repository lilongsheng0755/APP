<?php

namespace Config;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-10-9 22:30:34
 * Description: 文件上传配置
 */
class ConfigUpload {

    /**
     * 允许上传的文件类型
     *
     * @var array 
     */
    public static $allow_type = array('jpg', 'gif', 'png');

    /**
     * 允许上传文件的大小（KB）
     *
     * @var int 
     */
    public static $max_size = 500;

}
