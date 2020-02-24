<?php

namespace Lib\Request;

use Helper\HelperReturn;

/**
 * Author: skylong
 * CreateTime: 2019/10/30 0:06
 * Description: 路由调度
 */
class Route
{
    /**
     * @var Route 路由对象
     */
    private static $instance;

    /**
     * @var array 请求参数
     */
    private $request_params = [];

    /**
     * Route constructor.
     *
     * @param array $request_params
     */
    public function __construct($request_params = [])
    {
        if ($request_params) {
            $this->request_params = $request_params;
        }
    }

    /**
     * 单例模式实例化
     *
     * @return Route
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            if (REQUEST_SOURCE == 1) {
                self::$instance = new self($_REQUEST);
            } else {
                self::$instance = new self($_POST);
            }
            $_REQUEST = $_POST = []; // 重置变量，减少内存占用
        }
        return self::$instance;
    }

    /**
     * 路由初始化
     */
    public function init()
    {
        if (REQUEST_SOURCE == 1) {
            $this->handleRequestFromWeb();
        } else {
            $this->handleRequestFromApi();
        }
    }

    /**
     * 处理来至页面请求
     */
    private function handleRequestFromWeb()
    {
        $sss = explode('/', $this->request_params['sss']);
        $path_arr = [];

        // 路由参数过滤或初始化
        foreach ($sss as $s) {
            $s = str_replace(['/', "\\s+", '.html', '.php', '.htm', '.asp', '.aspx', '.jsp'], '', $s);
            if (!$s || strlen($s) > 30) {
                continue;
            }
            $path_arr[] = $s;
        }
        if (count($path_arr) < 4) {
            $path_arr = ['AdminCenter', 'Index', 'Index', 'index'];
        }

        // 实现路由调度
        list($apps_name, $module, $controller, $action) = $path_arr;
        if (in_array($module, ['Common'])) { // 禁止访问的模块
            header('HTTP/1.1 404 Not Found');
            exit();
        }
        $class = "Apps\\{$apps_name}\\{$module}\Controller\\{$controller}Controller";
        if (!class_exists($class)) {
            header('HTTP/1.1 404 Not Found');
            exit();
        }
        if (!method_exists($class, $action)) {
            header('HTTP/1.1 404 Not Found');
            exit();
        }
        define('REQUEST_APPS', $apps_name);
        define('REQUEST_MODULE', $module);
        define('REQUEST_CONTROLLER', $controller);
        define('REQUEST_ACTION', $action);
        $object = $class::getInstance();
        call_user_func([$object, $action], $this->request_params);
    }

    /**
     * 处理来至接口请求
     */
    private function handleRequestFromApi()
    {
        if (!$this->request_params = (array)json_decode($this->request_params['post_data'], true)) {
            HelperReturn::jsonData([], -100, '非法请求');
        }
        if (!isset($this->request_params['method']) || strlen($this->request_params['method']) > 50) {
            HelperReturn::jsonData([], -101, '非法请求'); // 缺少请求方法或非法请求方法
        }
        $method = explode('.', $this->request_params['method']); // 解析方法名：应用名称.模块名称.服务名称.接口名
        if (count($method) < 4) {
            HelperReturn::jsonData([], -102, '非法请求'); // 解析方法名错误
        }

        // 实现路由调度
        list($apps_name, $module, $service_name, $api) = $method;
        if (in_array($module, ['Common'])) { // 禁止访问的模块
            HelperReturn::jsonData([], -103, '非法请求'); // 访问不允许的模块
        }
        $class = "Apps\\{$apps_name}\\{$module}\\{$service_name}";
        if (!class_exists($class)) {
            HelperReturn::jsonData([], -104, '非法请求'); // 模块不存在
        }
        if (!method_exists($class, $api)) {
            HelperReturn::jsonData([], -105, '非法请求'); // 接口不存在
        }
        $object = $class::getInstance();
        call_user_func([$object, $api], $this->request_params);
    }
}