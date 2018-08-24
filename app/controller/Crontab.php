<?php
/**
 * 后台运行
 * 
 * @author: honglinzi
 * @version: 1.0
 */
namespace app\controller;

class Crontab
{
    //消息频道
    private $channel = 'orderChannel';
    //redis连接对象
    private $redis = null;    
    //redis IP
    private $host = '127.0.0.1';
    //redis 端口
    private $port = '6379';    
    public function __construct()
    {
        try
        {
            if (!class_exists('Redis'))
            {
                throw new \Exception('Redis扩展不存在');
            }
            $this->redis = new \Redis();
            $this->redis->pconnect($this->host, $this->port, 0);
        }
        catch (\Exception $e)
        {
            die($e->getMessage());
        }
    }    
    /**
     * 订阅消息
     * 
     * @return void
     */
    public function execute()
    {
        $this->redis->subscribe(array($this->channel), array($this, 'callback'));
    }
    /**
     * 接到消息后，回调处理
     * 
     * @return void
     */
    public function callback($redis, $channelName, $message)
    {
        echo $channelName, "==>", $message, PHP_EOL;
        
        //这里可以将队列保存进数据库
    }

}
