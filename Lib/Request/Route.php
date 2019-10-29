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
    private $params = [];

    /**
     * Route constructor.
     *
     * @param array $params
     */
    public function __construct($params = [])
    {
        if ($params) {
            $this->params = $params;
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
        return $this->params;
    }

    /**
     * 处理来至页面请求
     */
    private function handleRequestFromWeb()
    {
        var_dump($this->params);
    }

    /**
     * 处理来至接口请求
     */
    private function handleRequestFromApi()
    {
        var_dump($this->params);
    }
}