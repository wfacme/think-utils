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
     * 验证规则
     * @var array
     */
    protected $validateRules =[];

    /**
     * 错误信息
     * @var array
     */
    protected $validateErrors;

    /**
     * 验证场景
     * @var string
     */
    protected $validateScene;

    /**
     * 模型验证
     * @param array $data
     * @param bool $batch
     * @param null $scene
     * @return bool
     */
    public function validate($data=[],$batch=false,$scene=null):bool{
        if($data instanceof Request && is_object($data)){
            $data = $data->param();
        }
        $this->validateScene = $scene;
        $validate = $this->initValidate();
        $rules = $this->initRules($data);
        if(count($rules)>0){
            $rules = $this->initFieldName($rules);
            $validate = $validate->rule($rules);
            //提示信息
            if($message = $this->initMessage()){
                $validate->message($message);
            }
            if($batch) $validate->batch(true);
            $result = $validate->check($data);
            if(!$result){
                $this->validateErrors = $validate->getError();
                return false;
            }
        }
        $this->lazySave($data);
        return true;
    }

    /**
     * 初始化验证器
     * @return Validate
     */
    protected function initValidate(){
        if(property_exists($this,'validateClass')){
            $class = $this->validateClass;
            if(!empty($class) && ($class = new $class()) instanceof Validate){
                return $class;
            }
        }
        return new Validate();
    }

    /**
     * 验证数据
     * @param array $data
     * @return array
     */
    protected function initRules(&$data = []):array {
        $validateRules = [];
        if(!method_exists($this,'getRules')) return [];
        $rules = $this->getRules();
        foreach ($rules as $key=>$value){
            list ( $field,$rule ) = $value;
            $default = isset($value[2]) && !empty($value[2]) ? $value[2] : null;
            $scene = isset($value['on']) && !empty($value['on']) ? $value['on'] : null;
            if(is_array($field)){
                foreach ($field as $v){
                    !is_null($default) && $this->fieldHander($v,$data,$default);
                    $rule && $this->mergeRules($validateRules,$v,$rule,$scene);
                }
            }else{
                !is_null($default) && $this->fieldHander($field,$data,$default);
                $rule && $this->mergeRules($validateRules,$field,$rule,$scene);
            }
        }
        return $this->validateRules = $validateRules;
    }

    /**
     * @param array $rules
     * @return array
     */
    protected function initFieldName($rules=[]){
        if(method_exists($this,'getValidateFieldName')){
            foreach ($this->getValidateFieldName() as $field=>$title){
                if(isset($rules[$field])){
                    $rules[$field.'|'.$title] = $rules[$field];
                    unset($rules[$field]);
                }
            }
            return $rules;
        }
        return $rules;
    }

    /**
     * @param $name
     * @param $field
     * @param null $default
     * @return mixed|null
     */
    protected function fieldHander($name,&$field,$default=null){
        if(isset($field[$name])){
            $field[$name] = $default;
//            $request = new \think\Request();
//            if(!is_callable($default) && empty($field[$name])) {
//                $field[$name] = $default;
//            }else{
//                $request->filterValue($field[$name],$name,$default);
//            }
            return $field[$name];
        }
    }

    /**
     * @param $validateRules array 规则数组
     * @param $field string 字段
     * @param $rule string|array|callable 规则
     * @param null $scene
     */
    protected function mergeRules(&$validateRules,$field,$rule,$scene=null){
        if($this->validateScene && (is_null($scene) || !in_array($this->validateScene,$scene))) return;
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

    protected function initMessage(){
        if(method_exists($this,'getValidateMessage')){
            return $this->getValidateMessage();
        }
        return false;
    }

    /**
     * @return array
     */
    public function getErrors(){
        return $this->validateErrors;
    }

}
