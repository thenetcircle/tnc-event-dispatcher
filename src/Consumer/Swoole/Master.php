<?php

namespace Tnc\Service\EventDispatcher\Consumer\Swoole;

class Master
{
    // todo get return value for failed creation

    private $processes = [];
    private $fetchersNum;
    private $workersNumOfEachFetcher;

    public static function run($fetchersNum = 1, $workersNumOfEachFetcher = 1)
    {
        $instance = new self($fetchersNum, $workersNumOfEachFetcher);
        $instance->initName();
        $instance->initProcesses();

        $recv = \swoole_process::wait();
        echo "Shutdown" . PHP_EOL;
    }

    public function __construct($fetchersNum, $workersNumOfEachFetcher)
    {
        $this->fetchersNum             = $fetchersNum;
        $this->workersNumOfEachFetcher = $workersNumOfEachFetcher;
    }

    public function initName()
    {
        swoole_set_process_name('event-dispatcher master');
    }

    public function initProcesses()
    {
        for ($i = 0; $i < $this->fetchersNum; $i++) {

            $fetcher = Worker::createInstance($this);

        }
    }
}