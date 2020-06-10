<?php
/**
 * 时间戳格式化
 * User: qd_008
 * Date: 2020/5/13
 * Time: 16:06
 */
namespace acme\traits;


trait DateFormat
{

    /**
     * @var array 自动处理字段
     */
    protected static $formatTimeField = ['add_time','up_time','create_time','update_time','start_time','end_time'];

    /**
     * @param $model
     * @throws \ReflectionException
     */
    public static function onAfterRead($model)
    {
        $formatDates = array_merge(
            self::$formatTimeField,
            self::getDates()
        );
        foreach ($formatDates as $val){
            list ($name,$value) = self::_getDateFormat($model,$val);
            if($value)$model->setAttr($name,$value);
        }
    }

    /**
     * 字段格式化
     * @param $model
     * @param String $val
     * @return array
     * @throws \ReflectionException
     */
    public static function _getDateFormat($model,String $val)
    {
        if(preg_match('/\:/',$val)){
            list ($name,$val) = explode(':',$val);
            return [$name.'_format',date($val,$model->getAttr($name))];
        }else{
            $DomModel = new \ReflectionClass($model);
            $value = $model->getAttr($val) ?: false;
            if($value===false) return [$val,false];
            $value = $model->getData($val);
            if($DomModel->hasProperty('formatDomClass')){
                $ClassFormat = new \ReflectionClass(self::$formatDomClass);
                if($ClassFormat->implementsInterface(\acme\contracts\DateFormatContract::class)){
                    return [$val.'_format',call_user_func([$ClassFormat->newInstanceArgs(),'handle'],$model,$value)];
                }else{
                    return [$val.'_format',date("Y-m-d H:i:s",$value)];
                }
            }else{
                return [$val.'_format',date("Y-m-d H:i:s",$value)];
            }
        }
    }

    /**
     * 只作为替代
     * @return array
     */
    public static function getDates(){
        return [];
    }
}
