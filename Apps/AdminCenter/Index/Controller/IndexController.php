<?php
/**
 * Author: skylong
 * CreateTime: 2019/11/1 15:15
 * Description: 后台入口控制器
 */

namespace Apps\AdminCenter\Index\Controller;

use Apps\AdminCenter\Common\Controller\BaseController;

class IndexController extends BaseController
{
    /**
     * 后台主页
     *
     * @param array $request_params
     */
    public function indexView($request_params = [])
    {
        $tpl_vars = [];
        $this->assign($tpl_vars);
        $this->display();
    }

    /**
     * 首页内容
     *
     * @param array $request_params
     */
    public function welcomeView($request_params = [])
    {
        $tpl_vars = [];
        $this->assign($tpl_vars);
        $this->display();
    }


}