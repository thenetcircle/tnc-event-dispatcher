<?php

namespace Tnc\Service\EventDispatcher\Consumer\Swoole;

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
        $instance->process->useQueue(-1, 2);
        $instance->pid = $instance->process->start();

        return $instance;
    }

    public function run(\swoole_process $process)
    {
        echo 'Im a fetcher' . $this->getProcess()->pid . PHP_EOL;
        sleep(3);
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

    protected function initTasks()
    {
        for($i=0; $i<$this->taskNum; $i++)
        {
            $task = Task::createInstance();
            $this->tasks[$task->getPid()] = $task;
        }
    }
}