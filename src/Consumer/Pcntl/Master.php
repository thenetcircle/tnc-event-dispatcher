<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Master
{
    private static $pid;

    private $workerNum;
    private $taskNum;
    private $workers = [];
    private $queues  = [];
    private $tasks   = [];

    public static function run($workerNum = 1, $taskNum = 1)
    {
        self::$pid = getmypid();
        Process::setTitle('event-dispatcher master');

        $instance = new self($workerNum, $taskNum);
        $instance->initProcesses();

        while (($pid = pcntl_wait($status)) > 0) {
            echo 'Process Exit: ' . PHP_EOL;
            var_dump($pid);
        }

        var_dump($pid);

        echo "Shutdown" . PHP_EOL;
    }

    public static function getQueueKey($queueId)
    {
        $file = tempnam(sys_get_temp_dir(), 's');
        return ftok($file, 'a') + self::$pid + $queueId;
    }

    public function __construct($workerNum, $taskNum)
    {
        $this->workerNum = $workerNum;
        $this->taskNum   = $taskNum;
    }

    protected function initQueues()
    {
        for ($i = 1; $i <= $this->workerNum; $i++) {
            $queueKey                = self::getQueueKey($i);
            $this->queues[$queueKey] = msg_get_queue($queueKey, 0666);
        }
    }

    protected function initProcesses()
    {
        for ($i = 1; $i <= $this->workerNum; $i++) {

            $queueKey       = self::getQueueKey($i);
            $this->queues[] = $queueKey;

            $worker                           = Worker::createInstance($queueKey);
            $this->workers[$worker->getPid()] = $worker;

            for ($j = 1; $j <= $this->taskNum; $j++) {
                $task                         = Task::createInstance($queueKey);
                $this->tasks[$task->getPid()] = $task;
            }

        }
    }
}