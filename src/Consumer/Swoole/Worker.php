<?php

namespace Tnc\Service\EventDispatcher\Consumer\Swoole;

class Worker
{
    /**
     * @var int
     */
    private $pid;

    /**
     * @var Master
     */
    private $master;

    /**
     * @var \swoole_process
     */
    private $process;

    /**
     * @var int[]
     */
    private $workers = [];

    public static function createInstance(Master $master)
    {
        $instance = new self();

        $instance->master = $master;
        $instance->process = new \swoole_process(array($instance, 'task'));
        $instance->process->useQueue(-1, 2);
        $instance->pid = $instance->process->start();

        return $instance;
    }

    public function task(\swoole_process $process)
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

    public function push($message)
    {
        $this->getProcess()->push($message);
    }

    public function pop()
    {
        $this->getProcess()->pop(86186);
    }

    /**
     * @return array
     */
    public function getWorkers()
    {
        return $this->workers;
    }

    /**
     * @param $pid
     */
    public function addWorker($pid)
    {
        array_push($this->workers, $pid);
    }
}