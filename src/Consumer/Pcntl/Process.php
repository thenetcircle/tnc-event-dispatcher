<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

abstract class Process
{
    /**
     * @var int
     */
    private $pid;

    /**
     * @var Master
     */
    private $master;

    public static function fork(Master $master)
    {
        $instance         = new static();
        $instance->master = $master;

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

    private function __construct()
    {
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid ?: getmypid();
    }

    /**
     * @return Master
     */
    public function getMaster()
    {
        return $this->master;
    }

    public function getQueue()
    {
        return $this->master->getQueue();
    }

    abstract protected function run();
}