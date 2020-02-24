<?php

namespace Apps\AdminCenter\admin\Controller;

use Apps\AdminCenter\Common\Controller\BaseController;
use Helper\HelperReturn;

/**
 * Author: skylong
 * CreateTime: 2019/11/1 15:15
 * Description: 登录相关操作
 */
class LoginController extends BaseController
{
    /**
     * 用户登录页面
     */
    public function loginView()
    {
        $tpl_var = [];
        $this->assign($tpl_var);
        $this->display();
    }

    /**
     * 处理登录行为
     *
     * @param array $request_params 请求参数
     */
    public function doLoginApi($request_params = [])
    {
        HelperReturn::jsonData($request_params);
    }

    /**
     * 注销登录
     */
    public function logoutApi($request_params = [])
    {

    }
}