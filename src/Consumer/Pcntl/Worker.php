<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Worker
{
    /**
     * @var int
     */
    private $pid;

    /**
     * @var \swoole_process
     */
    private $process;

    /**
     * @var int
     */
    private $taskNum;

    /**
     * @var int[]
     */
    private $tasks = [];

    public static function createInstance($taskNum)
    {
        $instance = new self();

        $instance->taskNum = $taskNum;
        $instance->process = new \swoole_process(array($instance, 'run'));
        $instance->pid = $instance->process->start();

        return $instance;
    }

    public function run(\swoole_process $process)
    {
        $this->initName();
        $this->initTasks();

        echo 'Im a fetcher' . $this->getProcess()->pid . PHP_EOL;
        sleep(50);
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
        swoole_set_process_name('event-dispatcher worker');
    }

    protected function initTasks()
    {
        for($i=0; $i<$this->taskNum; $i++)
        {
            $task = Task::createInstance();
            $this->tasks[$task->getPid()] = $task;
        }
    }
}