<?php
namespace acme\basis;

use Closure;
use think\Container;
use Throwable,Exception;
use think\Pipeline as BasePipeline;

/**
 * Class Pipeline
 * @package acme\basis
 * @method $this through($pipes)
 * @method $this then(Closure $destination)
 * @method $this send($passable)
 */
class Pipeline extends BasePipeline
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $method = 'handle';

    /**
     * Pipeline constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Closure
     */
    protected function carry(){
        return function ($stack, $pipe){
            return function ($target) use ($stack, $pipe){
                try{
                    if($pipe instanceof Closure){
                        return call_user_func($pipe,$target,$stack);
                    }elseif(is_array($pipe)){
                        list ($class,$action) = $pipe;
                        return call_user_func_array([$this->container->make($class), $action],
                            [$target,$stack]
                        );
                    }else{
                        return call_user_func_array([$this->container->make($pipe), $this->method],
                            [$target,$stack]
                        );
                    }
                }catch (Throwable | Exception $e){
                    return $this->handleException($target, $e);
                }
            };
        };
    }
}
