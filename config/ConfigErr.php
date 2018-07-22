<?php

namespace config;

/**
 * Author: skylong
 * CreateTime: 2018-7-15 16:48:00
 * Description: 错误码配置类
 */
class ConfigErr {
    
    /**
     * memcached错误码
     *
     * @var array 
     */
    public static $mem_err = array(
        Memcached::RES_FAILURE,
        Memcached::RES_HOST_LOOKUP_FAILURE,
        Memcached::RES_UNKNOWN_READ_FAILURE,
        Memcached::RES_PROTOCOL_ERROR,
        Memcached::RES_CLIENT_ERROR,
        Memcached::RES_SERVER_ERROR,
        Memcached::RES_WRITE_FAILURE,
        Memcached::RES_DATA_EXISTS,
        Memcached::RES_NOTSTORED,
        Memcached::RES_NOTFOUND,
        Memcached::RES_PARTIAL_READ,
        Memcached::RES_SOME_ERRORS,
        Memcached::RES_NO_SERVERS,
        Memcached::RES_END,
        Memcached::RES_ERRNO,
        Memcached::RES_BUFFERED,
        Memcached::RES_TIMEOUT,
        Memcached::RES_BAD_KEY_PROVIDED,
        Memcached::RES_CONNECTION_SOCKET_CREATE_FAILURE,
        Memcached::RES_PAYLOAD_FAILURE
    );

}
