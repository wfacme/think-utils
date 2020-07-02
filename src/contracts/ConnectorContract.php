<?php
namespace acme\contracts;

/**
 * 接口配置契约
 * Interface ConnectorContract
 * @package acme\contracts
 */
interface ConnectorContract
{
    /**
     * 获取公众号配置
     * @return array
     */
    public function getOfficialAccountConfig() : array ;

    /**
     * 获取企业微信配置
     * @return array
     */
    public function getWorkConfig() : array ;

    /**
     * 获取小程序配置
     * @return array
     */
    public function getMiniProgramConfig() : array ;

    /**
     * 获取微信支付配置
     * @return array
     */
    public function getPaymentConfig() : array ;

    /**
     * 获取第三方平台配置
     * @return array
     */
    public function getOpenPlatformConfig() : array ;

    /**
     * 获取企业微信第三方服务商配置
     * @return array
     */
    public function getOpenWorkConfig() : array ;

    /**
     * 获取小微商户配置
     * @return array
     */
    public function getMicroMerchantConfig() : array ;

}
