<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Task extends Process
{
    protected function run()
    {
        self::setTitle('event-dispatcher task');

        echo 'Im a task' . $this->getPid() . PHP_EOL;
        echo $this->getQueueKey() . PHP_EOL;
        sleep(5);
    }
}