<?php

/**
 * 基础类，简单实现分层
 * 
 * @author: honglinzi
 * @version: 1.0
 */

namespace Core;

class Base
{

    private static $_control = '';
    private static $_action = '';

    public function __construct()
    {
        //注册类的自动加载
        if (function_exists('spl_autoload_register'))
        {
            spl_autoload_register(array($this, 'autoload'));
        }
    }

    /**
     * 入口
     * @access  public
     * 
     * @return  void
     */
    public function run()
    {
        $this->parsePath();
        $control = '\\app\\controller\\' . self::$_control;
        $action = self::$_action;

        if (class_exists($control))
        {
            $instance = new $control();
            //判断实例$instance中是否存在$action方法
            if (method_exists($instance, $action))
            {
                $instance->$action();
            }
            else
            {
                return "error action: $control :: $action";
            }
        }
        else
        {
            return "error controller : $control";
        }
    }

    /**
     * 解析URL获得控制器与方法
     * @access protected
     * 	 
     * @return void
     */
    protected function parsePath()
    {

        //这里判断控制器的值是否为空, 如果是空的使用默认的
        $_GET['c'] = !empty($_GET['c']) ? $_GET['c'] : 'Crontab';
        $_GET['a'] = !empty($_GET['a']) ? $_GET['a'] : 'execute';

        self::$_control = $_GET['c'];
        self::$_action = $_GET['a'];
    }

    /**
     * 自动加载类
     * @access protected	
     * @param   	string      	$className
     *
     * @return  bool
     */
    public function autoload($className)
    {
        if ($className)
        {
            $classFile = '';
            $classFile = './'.strtr($className, '\\', '/') . '.php';

            if (file_exists($classFile))
            {
                include($classFile);
                return true;
            }
        }
        return false;
    }

}
ini_set('display_errors', true);
$control = new \Core\Base();
echo $control->run();
