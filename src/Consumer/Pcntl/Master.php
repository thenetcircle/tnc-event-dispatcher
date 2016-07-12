<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Master
{
    private $pid;

    private $queue;

    private $fetchers = [];
    private $fetchersNum;
    private $workers  = [];
    private $workersNum;

    public function __construct($fetchersNum, $workersNum)
    {
        $this->fetchersNum = $fetchersNum;
        $this->workersNum  = $workersNum;
        $this->pid         = getmypid();
    }

    public function run()
    {
        printf("Master [%d] running.\n", $this->getPid());

        Utils::setProcessTitle('event-dispatcher master');

        $this->initQueue();
        $this->initFetchers();
        $this->initWorkers();

        while(($pid = pcntl_wait($status)) > 0) {
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

    public function getQueue()
    {
        return $this->queue;
    }

    protected function initQueue()
    {
        $key         = ftok(__FILE__, 'A'); // + $this->getPid();
        $this->queue = msg_get_queue($key, 0666);
    }

    protected function initFetchers()
    {
        for($i = 0; $i < $this->fetchersNum; $i++) {
            $fetcher                            = Fetcher::fork($this);
            $this->fetchers[$fetcher->getPid()] = $fetcher;
        }
    }

    protected function initWorkers()
    {
        for($i = 0; $i <= $this->workersNum; $i++) {
            $workers                           = Worker::fork($this);
            $this->workers[$workers->getPid()] = $workers;
        }
    }
}