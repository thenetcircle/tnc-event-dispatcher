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
     * @var ProcessManager
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
     * @param ProcessManager $manager
     * @param callable       $job
     * @param string|null    $title
     * @param int|null       $id
     */
    public function __construct(ProcessManager $manager, callable $job, $title = null, $id = null)
    {
        $this->id      = $id;
        $this->title   = $title;
        $this->job     = $job;
        $this->manager = $manager;
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
     * @return int
     */
    public function getParentPid()
    {
        if(function_exists('posix_getppid')) {
            return posix_getppid();
        }

        return 0;
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
     * @return callable
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return ProcessManager
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
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->getManager()->getLogger();
    }

    /**
     * run the real job
     */
    public function run()
    {
        call_user_func($this->job, $this);
    }
}