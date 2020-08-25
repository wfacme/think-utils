<?php
/**
 * 海报
 * User: qd_008
 * Date: 2020/6/9
 * Time: 8:27
 */

namespace acme\services;

use think\Exception;
use ReflectionClass;
use acme\contracts\CanvasContract;
use Intervention\Image\ImageManager;
use acme\exceptions\CanvasException;

class Canvas
{
    /**
     * 全局路径
     * @var string
     */
    public $filePath;

    /**
     * 背景宽度
     * @var number
     */
    public $width;

    /**
     * 背景高度
     * @var number
     */
    public $height;

    /**
     * @var ImageManager
     */
    private $canvas;

    /**
     * @var ImageManager
     */
    private $ImageManager;

    /**
     * 绘画元素
     * @var array
     */
    protected $elements = [];

    /**
     * 驱动类型
     * @var array
     */
    protected $drivers = ['GD','Imagick'];

    /**
     * @var String 全局字体
     */
    private $font = '/font/st-heiti-light.ttc';

    /**
     * 动作类型
     * @var array
     */
    private $action = ['text','image','rectangle','line','polygon','rectangle'];

    /**
     * Canvas constructor.
     * @param string $driver
     * @param array $config
     * @throws CanvasException
     */
    public function __construct($driver='GD',$config=[])
    {
        try{
            if(!in_array($driver,$this->drivers)){
                $class = $driver;
                if(is_string($driver)){
                    $class = new ReflectionClass($driver);
                    $class = $class->newInstance($config);
                }
                if( $class instanceof CanvasContract ){
                    $config = $class->getElements();
                    $driver = $class->driver;
                }
            }
            $this->elements = array_merge(
                $this->elements,
                $config
            );
            $this->ImageManager = new ImageManager(['driver'=>$driver]);
        }catch (\Exception $e){
            throw new CanvasException($e->getMessage(),$e->getLine());
        }
    }

    /**
     * 设置绘制元素
     * @param array $elements 绘画元素
     * @param bool $isStart 是否开始绘制
     * @throws CanvasException
     */
    public function setElement(Array $elements = [],Bool $isStart = true)
    {
        $this->elements = array_merge(
            $this->elements,
            $elements
        );
        $this->initCanvas($isStart);
    }


    /**
     * 初始化canvas
     * @param bool $isStart
     * @return Canvas|ImageManager
     * @throws CanvasException
     */
    public function initCanvas($isStart=true)
    {
        try{
            $config = [];
            $elements = $this->elements;
            if( isset($elements['config']) ){
                $config = $elements['config'];
                unset($this->elements['config']);
            }
            $this->_initParams($config);
            //初始化canvas
            if(isset($config['backgroundImage'])){
                $this->drawImageBackground(
                    $this->getPath($config['backgroundImage'])
                );
            }else{
                $this->drawPurityBackground(
                    isset($config['background']) ? $config['background'] : '#FFFFFF'
                );
            }
            return $isStart ? $this->startDraw() : $this;
        }catch (\Exception $e){
            throw new CanvasException($e->getMessage(),$e->getLine());
        }
    }

    /**
     * @return ImageManager
     * @throws CanvasException
     */
    public function startDraw(){
        $elements = $this->elements;
        foreach ($elements as $key=>$item){
            $this->_checkItem($item['type']);
            $this->_checkCanvasInit();
            $name = ucfirst($item['type']);
            call_user_func([$this,"add{$name}"],$item);
        }
        return $this->canvas;
    }

    /**
     * 文本添加
     * @param array $item
     * @return mixed
     */
    public function addText($item=[]){
        $item = $this->_getDefaultText($item);
        if($item['multiple']){
            $item['title'] = $this->wrapText(
                $item['size'],$item['angle'],$item['font'],$item['title'],$item['width'],$item['multiple']
            );
        }
        $canvas = $this->canvas->text(
            $item['title'],$item['left'],$item['top'],
            function ($font) use ($item){
                $font->file( $item['font'] );
                $font->size( $item['size'] );
                $font->color( $item['color'] );
                $font->align( $item['align'] );
                $font->valign( $item['valign'] );
                $font->angle( $item['angle'] );
            }
        );
        if(
            isset($item['call'])&&
            $item['call'] instanceof \Closure
        ){
            $item['call']($canvas);
        }
        return $canvas;
    }

    /**
     * 获取默认文本配置
     * @param array $val
     * @return array
     */
    private function _getDefaultText($val=[]){
        if(isset($val['font'])&&!empty($val['font'])){
            $val['font'] = $this->getPath($val['font']);
        }else{
            $val['font'] = $this->font();
        }
        return array_merge([
            'title'     => '测试文本',   //文本
            'multiple'  => false,       //最多显示多少行 默认不控制
            'width'     => $this->width,//宽度 默认为背景的宽度
            'left'      => 0,           //从图像左边缘到文本左边缘的距离 (可选)
            'top'       => 0,           //从图像上边缘到文本基线的距离 (可选)
            'font'      => 1,           //默认字体
            'align'     =>  'left',     //水平对齐
            'valign'    =>  'center',   //垂直对齐
            'size'      => 12,          //字体大小
            'color'     => '#333333',   //字体颜色
            'angle'     => 0,           //旋转角度
        ],$val);
    }

    /**
     * 添加图片
     * @param array $item
     * @return \Intervention\Image\Image
     * @throws CanvasException
     */
    public function addImage($item=[]){
        $item = $this->_getDefaultImage($item);
        $image = $this->ImageManager->make($item['src']);
        if(isset($item['size'])&&$item['size']!==false){
            $image->fit($item['size']==0?$this->width : $item['size']);
        }
        if($item['circular']){
            $image = $this->circularImage($image);
        }
        if(
            isset($item['call'])&&
            $item['call'] instanceof \Closure
        ){
            $item['call']($image);
        }
        $canvas = $this->canvas->insert(
            $image,$item['position'],
            $item['left'],$item['top']
        );
        return $image;
    }

    /**
     * 获取默认图片配置
     * @param array $val
     * @return array
     * @throws CanvasException
     */
    private function _getDefaultImage($val=[]){
        if(isset($val['src'])){
            $val['src'] = $this->getImagePath($val);
        }elseif(isset($val['source'])){
            $val['src'] = $val['source'];
        }else{
            throw new CanvasException("请选择图片");
        }
        return array_merge([
            'src'       => '',   //图片路径
            'opacity'   =>  1,
            'left'      =>  0,
            'top'       =>  0,
            'circular'  =>  false,
            'position'  =>  'top-left',
            'call'      =>  null
        ],$val);
    }

    /**
     * @param array $val
     * @return string
     * @throws CanvasException
     */
    public function getImagePath($val=[]){
        $path = $val['src'];
        $path = $this->getPath('/images/poster/poster.jpg');
        if(!empty($val['src'])){
            if(is_file($val['src'])){
                $path = $this->getPath($val['src']);
            }else if(is_file($this->getPath($val['src']))){
                $path = $this->getPath($val['src']);
            }
        }

        return $path;
    }

    /**
     * 验证类型
     * @param null $type
     * @return bool
     * @throws CanvasException
     */
    private function _checkItem($type=null){
        if(!in_array($type,$this->action)){
            throw new CanvasException("[{$type}：]不支持的绘制类型");
        }
        return true;
    }

    /**
     * 判断幕布是否初始化
     * @return ImageManager
     * @throws CanvasException
     */
    private function _checkCanvasInit(){
        if(!is_object($this->canvas)){
            throw new CanvasException("请先创建幕布");
        }
        return $this->canvas;
    }

    /**
     * 处理纯色背景
     * @param string $background
     * @return \Intervention\Image\Image|ImageManager
     */
    public function drawPurityBackground($background="#FFFFFF"){
        $this->canvas = $this->ImageManager->canvas($this->width,$this->height,$background);
        return $this->canvas;
    }

    /**
     * 处理图片背景
     * @param string $src
     * @return \Intervention\Image\Image|ImageManager
     */
    public function drawImageBackground($src=''){
        $image = $this->ImageManager->make($src);
        $width = $this->width ?: $image->width();
        $height = $this->height ?: $image->height();
        $this->canvas = $image->resize($width,$height);
        return $this->canvas;
    }

    /**
     * 参数配置初始化
     * @param array $config
     */
    private function _initParams($config=[]){
        if(isset($config['width'])){
            $this->width = $config['width'];
        }
        if(isset($config['height'])){
            $this->height = $config['height'];
        }
        if(isset($config['filePath'])){
            $this->filePath = $config['filePath'];
        }

    }

    /**
     * 圆形裁剪图像
     * @param $image
     * @return \Intervention\Image\Image
     */
    private function circularImage($image){
        //新建画布
        $w = $image->width();
        $h = $image->height();
        $w = $h = min($w, $h);
        $newImg = $this->ImageManager->canvas($w,$h);
        $r = $w /2;
        for($x=0;$x<$w;$x++) {
            for($y=0;$y<$h;$y++) {
                $c = $image->pickColor($x,$y);
                if(((($x-$r) * ($x-$r) + ($y-$r) * ($y-$r)) < ($r*$r))) {
                    $newImg->pixel($c,$x,$y);
                }
            }
        }
        return $newImg;
    }

    /**
     * 处理文字超出长度自动换行
     * @param integer $fontsize 字体大小
     * @param integer $angle 角度
     * @param string $fontface 字体名称
     * @param string $string 字符串
     * @param integer $width 预设宽度
     * @param null $max_line 最多行数
     * @return string
     */
    private function wrapText($fontsize, $angle, $fontface, $string, $width, $max_line = null)
    {
        // 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
        $content = "";
        // 将字符串拆分成一个个单字 保存到数组 letter 中
        $letter = [];
        for ($i = 0; $i < mb_strlen($string, 'UTF-8'); $i++) {
            $letter[] = mb_substr($string, $i, 1, 'UTF-8');
        }
        $line_count = 0;
        foreach ($letter as $l) {
            $testbox = imagettfbbox($fontsize, $angle, $fontface, $content . ' ' . $l);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                $line_count++;
                if ($max_line && $line_count >= $max_line) {
                    $content = mb_substr($content, 0, -1, 'UTF-8') . "...";
                    break;
                }
                $content .= "\n";
            }
            $content .= $l;
        }
        return $content;
    }

    /**
     * （设置/获取）全局路径
     * @param string $path
     * @return string
     */
    public function path($path=''){
        if(!empty($path)) $this->filePath = $path;
        return empty($this->filePath) ? app()->getRootPath().'../files' : $this->filePath;
    }

    /**
     * 获取组合路径
     * @param string $path
     * @return string
     */
    public function getPath($path=''){
        return $this->path().$path;
    }

    /**
     * 获取全局字体
     * @param string $font
     * @return string
     */
    public function font($font=''){
        if(!empty($font)) $this->font = $font;
        return $this->getPath($this->font);
    }

}
