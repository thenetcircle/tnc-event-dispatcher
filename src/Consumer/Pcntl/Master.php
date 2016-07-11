<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Master
{
    // todo get return value for failed creation

    private $workers = [];
    private $workerNum;
    private $taskNum;

    public static function run($workerNum = 1, $taskNum = 1)
    {
        $instance = new self($workerNum, $taskNum);
        $instance->initName();
        $instance->initWorks();

        sleep(10);

        while (false !== ($recv = \swoole_process::wait())) {
            echo 'Process Exit: ' . PHP_EOL;
            var_dump($recv);
        }

        echo "Shutdown" . PHP_EOL;
    }

    public function __construct($workerNum, $taskNum)
    {
        $this->workerNum = $workerNum;
        $this->taskNum   = $taskNum;
    }

    protected function initName()
    {
        swoole_set_process_name('event-dispatcher master');
    }

    public function initWorks()
    {
        for ($i = 0; $i < $this->workerNum; $i++) {
            $worker                           = Worker::createInstance($this->taskNum);
            $this->workers[$worker->getPid()] = $worker;
        }
    }
}