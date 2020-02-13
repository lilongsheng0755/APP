<?php


namespace Lib\Request;

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
     * 获取请求参数
     *
     * @return array
     */
    public function getRequestParams()
    {
        return $this->request_params;
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
        if (count($path_arr) < 3) {
            $path_arr = ['Index', 'Index', 'index'];
        }

        // 实现路由调度
        list($module, $controller, $action) = $path_arr;
        if (in_array($module, ['Common'])) { // 禁止访问的模块
            header('HTTP/1.1 404 Not Found');
            exit();
        }
        $class = "Apps\Admin\\{$module}\Controller\\{$controller}Controller";
        if (!class_exists($class)) {
            header('HTTP/1.1 404 Not Found');
            exit();
        }
        if (!method_exists($class, $action)) {
            header('HTTP/1.1 404 Not Found');
            exit();
        }
        $controller = "{$class}::getInstance";
        $object = $controller();
        call_user_func([$object, $action], $this->request_params);
    }

    /**
     * 处理来至接口请求
     */
    private function handleRequestFromApi()
    {
        if (!$this->request_params = (array)json_decode($this->request_params['post_data'], true)) {

        }
    }
}