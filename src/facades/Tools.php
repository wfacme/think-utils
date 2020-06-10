<?php
/**
 * Created by PhpStorm.
 * User: qd_008
 * Date: 2020/5/19
 * Time: 17:10
 */

namespace acme\facades;


class Tools extends \think\Facade
{
    protected static function getFacadeClass()
    {
        return \acme\services\Tools::class;
    }

}
