<?php
namespace acme\contracts;

/**
 * 接口配置契约
 * Interface ConnectorConfigContract
 * @package acme\contracts
 */
interface ConnectorConfigContract
{
    /**
     * 获取小程序配置
     * @return array
     */
    public function getWxappConfig() : array ;

    /**
     * 获取微信公众号配置
     * @return array
     */
    public function getWechatConfig() : array ;

}
