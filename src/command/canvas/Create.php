<?php
declare (strict_types = 1);

namespace acme\command\canvas;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Create extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('canvas:create')
            ->addArgument('path', Argument::OPTIONAL, "The generated template path（生成的模板路径）")
            ->addOption('--clear','c', Option::VALUE_NONE, "Concise template（简洁的模板）")
            ->setDescription('Create poster sample template（创建海报示例模板）');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim( $input->getArgument('path') );
        $classname = $this->getClassName($name);
        $pathname = $this->getPathName($classname);
        if (is_file($pathname)) {
            $output->writeln('<error>:' . $classname . ' already exists!</error>');
            return false;
        }
        if (!is_dir(dirname($pathname))) {
            mkdir(dirname($pathname), 0755, true);
        }

        file_put_contents($pathname, $this->buildClass($classname,$input));
        $output->writeln('<info>' . $classname . ' created successfully.</info>');
    }

    protected function buildClass(string $name,Input $input)
    {
        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
        $class = str_replace($namespace . '\\', '', $name);
        $replaceRules = ['{%className%}', '{%namespace%}', '{%app_namespace%}','{%arrayData%}','{%classDefault%}'];
        $replaceData = [
            $class,
            $namespace,
            $this->app->getNamespace(),
        ];
        if($input->hasOption('clear')){
            //简洁模板
            array_push($replaceData,'return [];');
            array_push($replaceData,'* 海报生成类');
        }else{
            array_push($replaceData,file_get_contents($this->getStub('array.stub')));
            array_push($replaceData,file_get_contents($this->getStub('default.stub')));
        }
        return str_replace($replaceRules, $replaceData, file_get_contents($this->getStub()));
    }


    protected function getStub($name='create.stub'): string
    {
        return __DIR__ . "/../stubs/" . $name;
    }

    protected function getClassName(string $name): string
    {
        if (strpos($name, '\\') !== false) {
            return $name;
        }
        return 'app\\common\\service\\canvas\\' . $name;
    }

    protected function getPathName(string $name): string
    {
        $name = str_replace('app\\', '', $name);
        return $this->app->getBasePath() . ltrim(str_replace('\\', '/', $name), '/') . '.php';
    }
}
