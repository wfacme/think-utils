<?php
/**
 * 日期处理类接口
 * User: qd_008
 * Date: 2020/5/15
 * Time: 17:48
 */

namespace acme\contracts;

use think\Model;

/**
 * 日期格式化契约
 * Interface DateFormatContract
 * @package acme\contracts
 */
interface DateFormatContract
{
    /**
     * 日期处理方法
     * @param Model $model
     * @param $val
     * @return mixed
     */
    public function handle(Model $model,$val);
}
