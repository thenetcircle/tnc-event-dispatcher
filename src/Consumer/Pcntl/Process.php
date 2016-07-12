<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Process
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var int
     */
    private $pid;

    /**
     * @var callable
     */
    private $job;

    public function __construct($id, callable $job, Manager $manager)
    {
        $this->id = $id;
        $this->manager = $manager;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     *
     * @return $this
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    public function run()
    {
        call_user_func($this->job, $this);
    }
}