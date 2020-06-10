<?php
declare (strict_types = 1);

namespace acme;

class AcmeService  extends \think\Service
{

	public $bind = [
        'pipeline' => \acme\basis\Pipeline::class,
    ];

    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {
    }

    
    /**
     * 执行服务
     *
     * @return mixed
     */
    public function boot()
    {
        $this->commands([
            'canvas:create'	 => \acme\command\canvas\Create::class,
        ]);
        $this->app->bind([
            'wechat' => \acme\services\Wechat::class,
        ]);
    }
}
