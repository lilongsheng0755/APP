<?php

namespace Apps\Api\Member;

use Apps\Api\Common\Service;
use Helper\HelperReturn;

/**
 * 用户相关接口
 */
class SMember extends Service
{
    public function login($request_params = []){
        HelperReturn::jsonData($request_params);
    }
}