<?php
/**
 * Author: skylong
 * CreateTime: 2019/11/1 15:15
 * Description: 后台入口控制器
 */

namespace Apps\AdminCenter\Index\Controller;

use Apps\AdminCenter\Common\Controller\Controller;

class IndexController extends Controller
{
    /**
     * 后台主页
     *
     * @param array $request_params
     */
    public function index($request_params = [])
    {
        var_dump($request_params);
    }
}