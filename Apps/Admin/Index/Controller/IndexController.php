<?php
/**
 * Author: skylong
 * CreateTime: 2019/11/1 15:15
 * Description: 后台入口控制器
 */

namespace Apps\Admin\Index\Controller;

use Apps\Admin\Common\Controller\Controller;

class IndexController extends Controller
{
    public function index($request_params = [])
    {
        var_dump($request_params);
    }
}