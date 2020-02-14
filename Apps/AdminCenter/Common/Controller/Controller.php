<?php
/**
 * Author: skylong
 * CreateTime: 2019/11/1 15:12
 * Description: 控制器基础类
 */

namespace Apps\AdminCenter\Common\Controller;

use Apps\AdminCenter\admin\Controller\LoginController;
use Helper\HelperSession;
use Lib\SPL\SplAbstract\ASingleBase;
use Load\LoadPlugs;

class Controller extends ASingleBase
{
    protected $tpl_name;

    /**
     * 继承父类单利模式
     *
     * @return object|Controller
     */
    public static function getInstance()
    {
        $controller = parent::getInstance(); // TODO: Change the autogenerated stub
        if (!$controller instanceof LoginController) {
            $controller->init();
        }
        return $controller;
    }

    /**
     * 后台初始化校验
     */
    public function init()
    {
        $userinfo = HelperSession::get('userinfo');
        if (!$userinfo) { // 校验登录信息是否失效
            header('location:/AdminCenter/Admin/Login/login');
            exit();
        }

        // 初始化smarty配置
        LoadPlugs::smarty()->setTemplateDir(PATH_APP . DS . 'Apps' . DS . REQUEST_APPS . DS . REQUEST_MODULE . DS . 'View' . DS);
        LoadPlugs::smarty()->setCompileDir(PATH_PUBLIC . DS . 'templates_c' . DS);
        LoadPlugs::smarty()->setConfigDir(PATH_PUBLIC . DS . 'configs' . DS);
        LoadPlugs::smarty()->setCacheDir(PATH_PUBLIC . DS . 'cache' . DS);
    }

    /**
     * 简化smarty模板变量传参
     *
     * @param array $tpl_var
     */
    public function assign($tpl_var = [])
    {
        LoadPlugs::smarty()->assign($tpl_var);
    }

    /**
     * 简化smarty执行模板渲染
     *
     * @param null $template
     */
    public function display($template = null)
    {
        $template = $template ? $template : REQUEST_COTROLLER . DS . REQUEST_ACTION . 'html';
        LoadPlugs::smarty()->display($template);
    }
}