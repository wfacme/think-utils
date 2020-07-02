<?php

namespace acme\services;

use think\App;
use think\Config;
use EasyWeChat\Factory;
use acme\exceptions\ConnectorException;
use acme\contracts\ConnectorContract;

class Wechat
{

    /**
     * 服务
     * @var array
     */
    private $services = [];

    /**
     * 配置类
     * @var ConnectorContract
     */
    protected $connector;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        Config $config,
        ConnectorContract $connector
    ){
        $this->connector = $connector;
        $this->config = $config;
    }

    public function connector() : void
    {
        try {
            $this->services = array_merge(
                $this->services,
                $this->config->get('wechat.services')
            );
            foreach ($this->services as $name=>$service){
                $className = parse_name($name,1);
                $configFunc = 'get' . $className .  'Config';
                $serviceClass =  Factory::make(
                    $className,
                    $this->connector->{$configFunc}()
                );
                $aliasName = 'wechat.'.$name;
                app()->bind($aliasName,$serviceClass);
                array_map(function ($item) use ($aliasName,$serviceClass){
                    app()->bind($aliasName.'.'.$item,$serviceClass->{$item});
                },$service);
            }
        }catch (\Exception | ConnectorException $e){
            throw new ConnectorException($e->getMessage(),$e->getCode());
        }
    }
}
