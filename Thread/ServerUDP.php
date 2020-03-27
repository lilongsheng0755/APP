<?php
/**
 * Author: skylong
 * CreateTime: 2020-03-26 14:07:58
 * Description: 基于swoole扩展的UDP服务
 */


namespace Thread;

use Config\ConfigLog;
use Lib\System\Log;
use Swoole\Server;

class ServerUDP
{

    /**
     * 启动UDP服务IP地址
     *
     * @var
     */
    private $udp_server_ip;

    /**
     * 启动UDP服务端口
     *
     * @var
     */
    private $udp_server_port;

    /**
     * UDP服务句柄
     *
     * @var Server
     */
    private $udp_server;

    /**
     * 收到的数据
     *
     * @var
     */
    private $receive_data;

    /**
     * 客户端信息
     *
     * @var
     */
    private $client_info;

    /**
     * 发送的数据
     *
     * @var
     */
    private $send_info;

    /**
     * redis实例
     *
     * @var
     */
    private $redis_conf;

    /**
     * 初始化udp服务配置
     *
     * @param string $udp_server_ip   启动udp服务的IP地址
     * @param int    $udp_server_port 启动udp服务的端口地址
     */
    public function __construct($udp_server_ip = '127.0.0.1', $udp_server_port = 50001, $send_info = [])
    {
        $this->udp_server_ip = $udp_server_ip ? $udp_server_ip : '127.0.0.1';
        $this->udp_server_port = $udp_server_port ? $udp_server_port : 50001;
        $this->send_info = $send_info ? $send_info : ['flag' => 1];
    }

    /**
     * 创建UPD服务
     */
    public function createServer()
    {
        $this->udp_server = new Server($this->udp_server_ip, $this->udp_server_port, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
        $this->udp_server->on('Packet', function ($udp_server, $receive_data, $client_info) {
            $this->receive_data = $receive_data;
            $this->client_info = $client_info;

            // 数据写入redis队列
            $redis = $this->getRedisInstance();
            $redis->lPush($this->redis_conf['redis_key'], $receive_data);
            $redis->close();
            unset($redis);

            $this->sendMsg($this->send_info);
        });
        $this->udp_server->start();
    }

    /**
     * 配置redis服务器信息，在执行createServer方法之前配置好
     *
     * @param string $redis_address  redis地址
     * @param int    $redis_port     redis端口
     * @param int    $timeout        redis等待时间（秒）
     * @param string $redis_password redis密码
     *
     * @return bool
     */
    public function setRedisConfig($redis_address = '', $redis_port = 6379, $timeout = 3, $redis_password = '')
    {
        if (!$redis_address) {
            return false;
        }

        $this->redis_conf['redis_address'] = $redis_address;
        $this->redis_conf['redis_port'] = $redis_port ? $redis_port : 6379;
        $this->redis_conf['timeout'] = $timeout ? $timeout : 3;
        $this->redis_conf['redis_password'] = $redis_password ? $redis_password : '';
        $this->redis_conf['redis_key'] = 'SERVER_UDP_LIST';
        return true;
    }

    /**
     * 设置redis队列key，用于udp协议接收到的数据入队列
     *
     * @param string $key
     *
     * @return bool
     */
    public function setRedisListKey($key = '')
    {
        if (!$key) {
            return false;
        }
        $this->redis_conf['redis_key'] = $key;
        return true;
    }

    /**
     * 获取Redis实例
     *
     * @return bool|\Redis
     */
    private function getRedisInstance()
    {
        try {
            if (!$this->redis_conf) {
                return false;
            }
            $redis = new \Redis();
            $redis->connect($this->redis_conf['redis_address'], $this->redis_conf['redis_port'], $this->redis_conf['timeout']);
            $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE); //Redis::SERIALIZER_NONE不序列化.Redis::SERIALIZER_IGBINARY二进制序列化
            $this->redis_conf['redis_password'] && $redis->auth($this->redis_conf['redis_password']);
        } catch (\RedisException $e) {
            $log['file'] = $e->getFile();
            $log['line'] = $e->getLine();
            $log['msg'] = $e->getMessage();
            $log['trace'] = $e->getTrace();
            Log::writeErrLog('err_server_upd', '[' . date('Y-m-d H:i:s') . ']:' . json_encode($log) . PHP_EOL, ConfigLog::ERR_REDIS_LOG_TYPE);
            return false;
        }
        return $redis;
    }

    /**
     * 发送数据包
     *
     * @param array $send_data
     */
    private function sendMsg($send_data = [])
    {
        if (!is_array($send_data)) {
            $send_data = ['flag' => 1, 'msg' => (string)$send_data];
        }
        $this->udp_server->sendto($this->client_info['address'], $this->client_info['port'], json_encode($send_data));
    }
}