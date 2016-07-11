<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Task
{
    /**
     * @var int
     */
    private $pid;

    /**
     * @var \swoole_process
     */
    private $process;

    public static function createInstance()
    {
        $instance = new self();

        $instance->process = new \swoole_process(array($instance, 'run'));
        $instance->process->useQueue(-1, 2);
        $instance->pid = $instance->process->start();

        return $instance;
    }

    public function run(\swoole_process $process)
    {
        $this->initName();
        echo 'Im a task' . $this->getProcess()->pid . PHP_EOL;
        sleep(100);
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return \swoole_process
     */
    public function getProcess()
    {
        return $this->process;
    }

    protected function initName()
    {
        swoole_set_process_name('event-dispatcher task');
    }
}