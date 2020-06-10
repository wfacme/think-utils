<?php
namespace acme\contracts;

/**
 * 绘图接口
 * Class CanvasContract
 * @package acme\contracts
 */
abstract class CanvasContract
{
    /**
     * 驱动类型
     * @var string
     */
    public $driver = 'GD';

    /**
     * 获取绘画元素
     * @return array
     */
    abstract public function getElements():array ;
}
