<?php
/**
 * Created by PhpStorm.
 * User: qd_008
 * Date: 2020/5/15
 * Time: 17:59
 */

namespace acme\services;


use think\Model;
use acme\contracts\DateFormatContract;

class SignUp implements DateFormatContract
{
    public function handle(Model $model, $val)
    {
        if(date('Y',time())==date('Y',$val)){
            return date('m/d H:i',$val);
        }else{
            return date('Y/m/d H:i',$val);
        }
    }
}
