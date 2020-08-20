<?php
namespace acme;

use acme\core\Validate;
use think\Config;
use think\facade\App;
use acme\services\Wechat;
use think\Service as BaseService;

class AcmeService extends BaseService
{
    public $bind = [
        'pipeline'          => \acme\basis\Pipeline::class,
        'migrate_creator'   => \acme\services\Creator::class,
    ];

    private $commands = [
        \acme\command\canvas\Create::class,
        \acme\command\Migration\Create::class,
    ];

    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register( )
    {
        $this->registerCommands();
    }

    /**
     * 执行服务
     *
     * @return mixed
     */
    public function boot(
        Config $config
    ) {
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
