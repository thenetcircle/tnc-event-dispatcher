<?php

namespace Tnc\Service\EventDispatcher\Consumer;

class Manager
{
    private $pid;
    private $queues    = [];
    private $processes = [];

    public function __construct($title)
    {
        $this->pid = getmypid();
        $this->setTitle($title);
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function addQueue(Queue $queue)
    {
        $this->queues[$queue->getName()] = $queue;

        return $this;
    }

    public function getQueue($name)
    {
        return $this->queues[$name];
    }

    public function spawn($id, $title, callable $job)
    {
        $process = new Process($id, $title, $job, $this);

        if(($pid = pcntl_fork()) === 0) {
            $this->setTitle($title);
            $process->setPid(getmypid())->run();
            exit(0);
        } elseif($pid === -1) {
            throw new \RuntimeException('{Manager:spawn} Failure on pcntl_fork.');
        }

        $process->setPid($pid);
        $this->processes[$pid] = $process;

        return $pid;
    }

    public function wait()
    {
        while(($pid = pcntl_wait($status)) > 0) {
            printf("Process [%d] exited.\n", $pid);
        }
    }

    protected function setTitle($title)
    {
        /*if(function_exists('cli_set_process_title')) {
            cli_set_process_title($title); //PHP >= 5.5.
        } else if(function_exists('setproctitle')) {
            setproctitle($title); //PECL proctitle
        }*/
    }
}