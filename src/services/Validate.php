<?php
/**
 * 验证器
 * User: qd_008
 * Date: 2020/5/29
 * Time: 11:01
 */

namespace acme\services;

use think\Model;
use ReflectionClass;
use ReflectionMethod;
use think\helper\Str;

class Validate extends \think\Validate
{

    protected $model;

    public function setModel(Model $model){
        $this->model = $model;
        $this->initFunc();
    }

    protected function initFunc(){
        $model = $this->model;
        $class = new ReflectionClass($model);
        $list = $class->getMethods(ReflectionMethod::IS_STATIC);
        foreach ($list as $val){
            if(preg_match('/^check.*attr$/i',$val->name,$preg)){
                $this->extend($val->name,function ($item) use ($val,$model){
                    $class = new $model();
                    return $class->{$val->name}($item);
                });
            }
        }
    }
}
