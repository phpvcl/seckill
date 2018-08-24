<?php

/**
 * 商品秒杀演示
 * 
 * @author: honglinzi
 * @version: 1.0
 */

namespace app\controller;

class Demo
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
            $this->redis->connect($this->host, $this->port);
        }
        catch (\Exception $e)
        {
            die($e->getMessage());
        }
    }

    /*
     * 商品详细页面
     * 
     * return string
     */

    public function index()
    {
        echo $this->seckill();
    }

    /*
     * 处理商品秒杀
     * 
     * return string
     */

    public function seckill()
    {
        session_start();
        $price = 100;
        //$userId = $_SESSION['user_id'];
        $userId = rand(1, 10000);

        $goodsId = 111;
        $result = ['code' => 0, 'msg' => '秒杀结束了'];
        if ($userId)
        {
            $orderNo = date('YmdHis') . $userId;
            //限制一个用户只能抢一个
            // $key = session_id();
            $key = substr(md5($orderNo), 8, 16);

            if (!$this->redis->exists($key) && $this->redis->decr('inventory') > -1)
            {
                $data = ['order_no' => $orderNo, 'user_id' => $userId, 'goods_id' => $goodsId, 'price' => $price];
                if ($this->redis->hMset($key, $data))
                {
                    //加入队列
                    $this->redis->lPush("orders:$goodsId", $key);
                    //发布一条消息给后台处理
                    $this->redis->publish($this->channel, $key);
                    $result = ['code' => 1, 'msg' => '秒杀成功'];
                }
                else
                {
                    $result = ['code' => 3, 'msg' => '秒杀失败，请重试'];
                }
            }
        }
        else
        {
            $result = ['code' => -1, 'msg' => '请先登陆'];
        }
        return json_encode($result);
    }

    /*
     * 支付回调接口
     * 
     * return bool
     */

    public function payNotify()
    {
        //回调处理
    }

    /*
     * 预设库存数量
     * 
     * return void
     */

    public function setInventory()
    {
        $this->redis->set('inventory', 20);
    }

    //清空
    public function flush()
    {
        $this->redis->flushDB();
    }

}
