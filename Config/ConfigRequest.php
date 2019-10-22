<?php

namespace Config;


/**
 * Author: lilongsheng
 * CreateTime: 2019/10/10 17:31
 * Description: HTTP请求相关配置
 */
class ConfigRequest
{
    /**
     * GET请求 从服务器获取数据
     */
    const REQUEST_METHOD_GET = 'GET';

    /**
     * POST请求 向服务器发送所需要处理的数据
     */
    const REQUEST_METHOD_POST = 'POST';

    /**
     * HEAD请求 获取与GET方法相应的头部信息
     */
    const REQUEST_METHOD_HEAD = 'HEAD';

    /**
     * PUT请求 更新或者替换一个现有的资源
     */
    const REQUEST_METHOD_PUT = 'PUT';

    /**
     * DELETE请求 删除一个服务器上的资源
     */
    const REQUEST_METHOD_DELETE = 'DELETE';

    /**
     * TRACE请求 对传到服务器上的头部信息进行追踪
     */
    const REQUEST_METHOD_TRACE = 'TRACE';

    /**
     * OPTION请求 获取该服务器支持的获取资源的http方法
     */
    const REQUEST_METHOD_OPTION = 'OPTIONS';

    /**
     * HTTP请求类型
     *
     * @var array
     */
    public static $map_request_method = [
        self::REQUEST_METHOD_GET    => 'GET 请求',
        self::REQUEST_METHOD_POST   => 'POST 请求',
        self::REQUEST_METHOD_HEAD   => 'HEAD 请求',
        self::REQUEST_METHOD_PUT    => 'PUT 请求',
        self::REQUEST_METHOD_DELETE => 'DELETE 请求',
        self::REQUEST_METHOD_TRACE  => 'TRACE 请求',
        self::REQUEST_METHOD_OPTION => 'OPTION 请求',
    ];
}