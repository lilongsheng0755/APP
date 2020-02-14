<?php
/**
 * Author: skylong
 * CreateTime: 2019/11/1 15:12
 * Description: 控制器基础类
 */

namespace Apps\Admin\Common\Controller;

use Helper\HelperSession;
use Lib\SPL\SplAbstract\ASingleBase;

class Controller extends ASingleBase
{
    /**
     * 继承父类单利模式
     *
     * @return object|Controller
     */
    public static function getInstance()
    {
        $controller = parent::getInstance(); // TODO: Change the autogenerated stub
        $controller->init();
        return $controller;
    }

    /**
     * 后台初始化校验
     */
    public function init()
    {
        $userinfo = HelperSession::get('userinfo');
        if (!$userinfo) {
            header('location:/User/Login/login');
            exit();
        }
    }
}