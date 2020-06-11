<?php

namespace acme\services;

use acme\contracts\ConnectorConfigContract;
use acme\exceptions\ConnectorException;
use EasyWeChat\Factory;

class Wechat
{

    /**
     * 配置读取接口
     * @var ConnectorConfigContract
     */
    protected $config;

    protected $wxapp;

    protected $wxchat;


    public function connector(ConnectorConfigContract $api=null,$type=[])
    {
        try {
            if(is_null($api)) throw new ConnectorException("无配置读取接口");
            $this->config = $api;
            if(!empty($type)){
                foreach ($type as $val){
                    if($val=='wxapp'){
                        $this->wxapp =  $this->wxapp();
                    }elseif($val=='wechat'){
                        $this->wechat = $this->wechat();
                    }
                }
            }
        }catch (\Exception | ConnectorException $e){
            throw new ConnectorException($e->getMessage(),$e->getCode());
        }
    }

    public function wxapp(){
        try {
            if(empty($this->wxapp)){
                $config = $this->config;
                $this->wxapp = Factory::miniProgram($config::getWechatConfig());
            }
            return $this->wxapp;
        }catch (\Exception | ConnectorException $e){
            throw new ConnectorException($e->getMessage(),$e->getCode());
        }
    }

    public function wechat(){
        try {
            if(empty($this->wechat)){
                $config = $this->config;
                $this->wechat = Factory::officialAccount($config::getWechatConfig());
            }
            return $this->wechat;
        }catch (\Exception | ConnectorException $e){
            throw new ConnectorException($e->getMessage(),$e->getCode());
        }
    }

    public function subscribe(){
        return $this->wxapp()->subscribe_message;
    }

    public function getTemplates($key=false){
        $templates = $this->subscribe()->getTemplates();
        if($templates['errmsg']!='ok')  throw new ConnectorException("获取模板失败");
        $templates = $templates['data'];
        return $key ? array_column($templates,$key) : $templates;
    }

    public function getCategory()
    {
        $templates = $this->subscribe()->getCategory();
        return isset($templates['data']) ? array_column($templates['data'],'id') : [];
    }

}
