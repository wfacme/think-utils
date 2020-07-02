<?php
namespace acme;

use think\Config;
use think\facade\App;
use acme\services\Wechat;
use think\Service as BaseService;

class AcmeService extends BaseService
{
    public $bind = [
        'pipeline'  => \acme\basis\Pipeline::class,
        'wechat'    => \acme\services\Wechat::class,
    ];

    private $commands = [
        \acme\command\canvas\Create::class,
//        \acme\command\make\Migrate::class,
    ];

    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register( )
    {
        $this->registerCommands();
        if(!empty(config('wechat.class'))) {
            $this->app->bind(
                \acme\contracts\ConnectorContract::class,
                config('wechat.class')
            );
        }
    }

    /**
     * 执行服务
     *
     * @return mixed
     */
    public function boot(
        Config $config
    ) {
        if(!empty($config->get('wechat.class'))){
            app('wechat')->connector();
        }
    }

    /**
     * 注册命令行
     *
     * @return mixed
     */
    private function registerCommands(){
        $this->commands($this->commands);
    }
}
