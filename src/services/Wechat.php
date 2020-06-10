<?php

namespace acme\services;

use acme\exceptions\ConnectorException;
use app\common\service\ApiConfig;
use EasyWeChat\Factory;

class Wechat
{
    protected $wxapp;

    public function wxapp(){
        try {
            if(!$this->wxapp){
                $connector = new Connector(new ApiConfig());
                $this->wxapp = Factory::miniProgram($connector::getWechatWxappConfig());
            }
            return $this->wxapp;
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
