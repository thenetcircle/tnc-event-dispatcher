<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Manager
{
    private $pid;

    private $queues = [];
    private $processes = [];

    private $jobQueue;
    private $receiptQueue;

    private $fetchers = [];
    private $fetchersNum;
    private $workers  = [];
    private $workersNum;

    public function __construct($fetchersNum, $workersNum)
    {
        $this->pid         = getmypid();

        $this->fetchersNum = $fetchersNum;
        $this->workersNum  = $workersNum;

    }

    public function run()
    {
        printf("Master [%d] running.\n", $this->getPid());

        Utils::setProcessTitle('event-dispatcher master');

        $this->initQueues();
        $this->initFetchers();
        $this->initWorkers();

        while (($pid = pcntl_wait($status)) > 0) {
            echo 'Process Exit: ' . PHP_EOL;
            var_dump($pid);
        }

        var_dump($pid);

        echo "Shutdown" . PHP_EOL;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function addQueue(Queue $queue)
    {
        $this->queues[$queue->getName()] = $queue;
    }

    public function getQueue($name)
    {
        return $this->queues[$name];
    }

    public function spawn($id, callable $job)
    {
        $process = new Process($id, $job, $this);

        if(($pid = pcntl_fork()) === 0) {
            $process->setPid(getmypid())->run();
            exit(0);
        }
        elseif ($pid === -1) {
            throw new \RuntimeException('{Manager:spawn} Failure on pcntl_fork.');
        }

        $process->setPid($pid);

        $instance->pid = $pid;
        return $instance;
    }












    public function getJobQueue()
    {
        return $this->jobQueue;
    }

    public function getReceiptQueue()
    {
        return $this->receiptQueue;
    }

    public function getFetchersNum()
    {
        return $this->fetchersNum;
    }

    public function getWorkersNum()
    {
        return $this->workersNum;
    }

    protected function initQueues()
    {
        $key                = ftok(__FILE__, 'A'); // + $this->getPid();
        $this->jobQueue     = msg_get_queue($key, 0666);
        $this->receiptQueue = msg_get_queue($key + 10, 0666);
    }

    protected function initFetchers()
    {
        for ($i = 1; $i <= $this->fetchersNum; $i++) {
            $fetcher                            = Fetcher::fork($this, $i);
            $this->fetchers[$fetcher->getPid()] = $fetcher;
        }
    }

    protected function initWorkers()
    {
        for ($i = 1; $i <= $this->workersNum; $i++) {
            $workers                           = Worker::fork($this, $i);
            $this->workers[$workers->getPid()] = $workers;
        }
    }
}