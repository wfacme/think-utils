<?php
namespace acme\command\migration;

use acme\services\Creator;
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
        $this->setName('make:migration')
            ->addArgument('name', Argument::REQUIRED, 'What is the name of the migration?')
            ->addOption('--comment', "-c", Option::VALUE_REQUIRED, 'table commment')
            ->setDescription('Create a new migration（创建新的迁移文件）');
    }

    /**
     * Create the new migration.
     *
     * @param Input  $input
     * @param Output $output
     * @return void
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function execute(Input $input, Output $output)
    {
        /** @var Creator $creator */
        $creator = $this->app->get('migrate_creator');

        $className = $input->getArgument('name');

        $comment = $input->getOption('comment');

        $path = $creator->create($className,$comment);

        $output->writeln('<info>created</info> .' . str_replace(getcwd(), '', realpath($path)));
    }


}
