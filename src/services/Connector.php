<?php
/**
 * Created by PhpStorm.
 * User: qd_008
 * Date: 2020/5/19
 * Time: 16:31
 */

namespace acme\services;

use acme\contracts\ConnectorConfigContract;
use acme\exceptions\ConnectorException;

/**
 * Class Connector
 * @package acme\services
 * @method self getWeChatConfig() 获取微信公众号序配置
 * @method self getWechatWxappConfig() 获取微信小程序配置
 */
class Connector
{

    /**
     * 微信配置信息
     * @var array
     */
    protected static $config;

    public function __construct(ConnectorConfigContract $config)
    {
        //初始化所有配置
        $mark = $config->getMark();
        if($mark===true){
            foreach ($config->getConfig() as $val){
                if(!isset($val[$mark])) return false;
                self::$config[$val[$mark]] = $val;
            }
        }else{
            self::$config = $config->getConfig();
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return array|mixed|string[]|\string[][]
     * @throws ConnectorException
     */
    public static function __callStatic($name, $arguments)
    {
        if(!preg_match('/^get(.*)Config$/i',$name,$class)){
            throw new ConnectorException("方法不存在");
        }
        $mark = parse_name($class[1]);
        if(empty(self::$config)) {
            throw new ConnectorException("当前配置信息不存在");
        }
        if(!array_key_exists($mark,self::$config)){
            throw new ConnectorException("标识[$mark]不存在");
        }
        return self::getConfig($mark);
    }

    /**
     * @param null $mark
     * @return array|mixed|string[]|\string[][]
     */
    protected static function getConfig($mark=null){
        if(is_null($mark)) return self::$config;
        return isset(self::$config[$mark]) ? self::$config[$mark] : [];
    }
}
