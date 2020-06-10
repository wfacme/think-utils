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
     * 获取所有配置
     * @return mixed
     */
    public function getConfig();

    /**
     * 获取配置标识
     * @return mixed
     */
    public function getMark();

}
