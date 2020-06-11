<?php
namespace acme;

use think\Service as BaseService;

class AcmeService extends BaseService
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
        $this->commands([
            'canvas:create'	 => \acme\command\canvas\Create::class,
        ]);
    }


    /**
     * 执行服务
     *
     * @return mixed
     */
    public function boot()
    {

    }
}
