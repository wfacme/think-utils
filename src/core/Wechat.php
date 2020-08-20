<?php
/**
 * Created by PhpStorm.
 * User: qd_008
 * Date: 2020/8/20
 * Time: 15:58
 */

namespace acme\core;

use acme\contracts\ConnectorContract;
use EasyWeChat\Factory;

/**
 * Class Wechat
 * @package acme\core
 *
 * @method static \EasyWeChat\Payment\Application            payment(array $config)
 * @method static \EasyWeChat\MiniProgram\Application        miniProgram(array $config)
 * @method static \EasyWeChat\OpenPlatform\Application       openPlatform(array $config)
 * @method static \EasyWeChat\OfficialAccount\Application    officialAccount(array $config)
 * @method static \EasyWeChat\BasicService\Application       basicService(array $config)
 * @method static \EasyWeChat\Work\Application               work(array $config)
 * @method static \EasyWeChat\OpenWork\Application           openWork(array $config)
 * @method static \EasyWeChat\MicroMerchant\Application      microMerchant(array $config)
 */
class Wechat
{

    /**
     * 获取微信配置
     * @param string $name
     * @param array $config
     * @return array
     */
    public static function config($name='',$config=[])
    {
        $singleConfig = [];
        $wechatConfig = config('acme.wechat.config');
        if(is_array($wechatConfig)){
            $singleConfig = isset($wechatConfig[$name]) ? $wechatConfig[$name] : [];
        }else if( is_object($wechatConfig) ){
            $wechat = new $wechatConfig;
            if($wechat instanceof ConnectorContract){
                $singleConfig = call_user_func([$wechat,$name]);
            }
        }
        return array_merge($singleConfig,$config);
    }

    /**
     * @param $action
     * @param $args
     * @return \EasyWeChat\Kernel\ServiceContainer
     */
    public static function __callStatic($action,$args)
    {
        $config = self::config($action,$args);
        return Factory::make($action,$config);
    }

}
