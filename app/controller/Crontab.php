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
    private $channel = 'orderChannel';
    /**
     * 订阅消息
     * 
     * @return void
     */
    public function execute()
    {
        $redis = new \Redis();
        $redis->pconnect('127.0.0.1', 6379, 0);
        $redis->subscribe(array($this->channel), array($this, 'callback'));
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
