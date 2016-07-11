<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Worker extends Process
{
    protected function run()
    {
        self::setTitle('event-dispatcher worker');

        echo 'Im a worker' . $this->getPid() . PHP_EOL;
        echo $this->getQueueKey() . PHP_EOL;
        sleep(10);
    }
}