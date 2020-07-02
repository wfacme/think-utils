<?php
/**
 * 输入验证
 * User: qd_008
 * Date: 2020/5/16
 * Time: 18:00
 */

namespace acme\traits;

use think\Request;
use acme\services\Validate;

trait ValidateFilter
{

    /**
     * 验证字段名称
     * @var array
     */
    private $validateFieldName = [];

    /**
     * 验证规则
     * @var array
     */
    private $validateRules = [];

    private function getValidate(): Validate
    {
        if(property_exists($this,'validateClass')){
            $class = $this->validateClass;
            if(!empty($class) && ($class = new $class()) instanceof Validate){
                return $class;
            }
        }
        return new Validate();
    }

    protected function checkData(): void
    {
        $data = $this->getData();
        try{
            $validate = $this->getValidate();
            $rules = $this->initRules();
            if(count($rules)>0){
                $rules = $this->initFieldName($rules);
                $validate = $validate->rule($rules);
                //提示信息
                if($message = $this->initMessage()){
                    $validate->message($message);
                }
                $result = $validate->check($data);
                if($result!==true){
                    throw new ValidateException($validate->getError());
                }
            }
        }catch (ValidateException $e){
            throw new ValidateException($e->getMessage(),$e->getLine());
        }
    }

    private function initRules():array
    {
        $validateName = [];
        $validateRules = [];
        if(!method_exists($this,'getRules')) return [];
        $rules = $this->getRules();
        foreach ($rules as $key=>$value){
            list ( $field,$rule ) = $value;
            if(isset($value['name'])){
                $name = is_array($value['name']) ? $value['name'] : [$value['name']];
                if(is_array($field)){
                    foreach ($field as $fieldKey=>$fieldName){
                        $name[$fieldKey] && (
                            $validateName[$fieldName] = $name[$fieldKey]
                        );
                        $rule && $this->mergeRules($validateRules,$fieldName,$rule);
                    }
                }else{
                    $rule && $this->mergeRules($validateRules,$field,$rule);
                }
            }
        }
        return $this->validateRules = $validateRules;
    }

    /**
     * @param array $rules
     * @return array
     */
    protected function initFieldName($rules=[]) : array
    {
        foreach ($this->validateFieldName as $field=>$title){
            if(isset($rules[$field])){
                $rules[$field.'|'.$title] = $rules[$field];
                unset($rules[$field]);
            }
        }
        return $rules;
    }

    /**
     * @param $validateRules array 规则数组
     * @param $field string 字段
     * @param $rule string|array|callable 规则
     */
    protected function mergeRules(&$validateRules,$field,$rule){
        if(isset( $validateRules[$field]) &&  $validateRules[$field]!=$rule){
            if(is_array($validateRules[$field])){
                $validateRules[$field] = array_merge($validateRules[$field],$rule);
            }else{
                $validateRules[$field] .= "|".$rule;
            }
        } else{
            $validateRules[$field] = $rule;
        }
    }

    /**
     * 初始化验证消息
     * @return bool
     */
    protected function initMessage(){
        if(method_exists($this,'getValidateMessage')){
            return $this->getValidateMessage();
        }
        return false;
    }
    
}
