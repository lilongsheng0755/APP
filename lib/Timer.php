<?php

namespace Lib;

/**
 * Author: skylong
 * CreateTime: 2018-8-11 13:01:12
 * Description: 计算脚本运行时所需要的时间
 */
class Timer {

    /**
     * 保存脚本开始执行时的时间（单位：微妙）
     *
     * @var int 
     */
    private $start_time = 0;

    /**
     * 保存脚本结束执行时的时间（单位：微妙）
     *
     * @var int
     */
    private $stop_time = 0;

    /**
     * 在脚本开始处调用获取脚本开始时间的微妙值
     */
    public function start() {
        $this->start_time = microtime(true);
    }

    /**
     * 在脚本结束处调用获取脚本结束时间的微妙值
     */
    public function stop() {
        $this->stop_time = microtime(true);
    }

    /**
     * 计算脚本执行所需要的时间
     * 
     * @return float
     */
    public function spent() {
        return round(($this->stop_time - $this->start_time), 4);
    }

}
