<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

abstract class Process
{
    /**
     * @var int
     */
    private $pid;

    /**
     * @var string
     */
    private $queueKey;

    public static function createInstance($queueKey)
    {
        $instance = new static();
        $instance->queueKey = $queueKey;

        if(($pid = pcntl_fork()) === 0) {
            $instance->run();
            exit(0);
        }
        elseif ($pid === -1) {
            throw new \RuntimeException('Failure on pcntl_fork');
        }

        $instance->pid = $pid;
        return $instance;
    }

    public static function setTitle($title)
    {
        if(function_exists('cli_set_process_title')) {
            cli_set_process_title($title); //PHP >= 5.5.
        } else if(function_exists('setproctitle')) {
            setproctitle($title); //PECL proctitle
        }
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid ?: getmypid();
    }

    public function getQueueKey()
    {
        return $this->queueKey;
    }

    abstract protected function run();
}