<?php

namespace Tnc\Service\EventDispatcher\Consumer;

class Process
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

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


    /**
     * Process constructor.
     *
     * @param int      $id
     * @param string   $title
     * @param callable $job
     * @param Manager  $manager
     */
    public function __construct($id, $title, callable $job, Manager $manager)
    {
        $this->id      = $id;
        $this->title   = $title;
        $this->job     = $job;
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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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

    /**
     * @param string $name
     *
     * @return Queue
     */
    public function getQueue($name)
    {
        return $this->getManager()->getQueue($name);
    }

    /**
     * run the real job
     */
    public function run()
    {
        call_user_func($this->job, $this);
    }
}