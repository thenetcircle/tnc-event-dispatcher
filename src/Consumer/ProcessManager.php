<?php

namespace Tnc\Service\EventDispatcher\Consumer;

use Psr\Log\LoggerInterface;
use Tnc\Service\EventDispatcher\SimpleLogger;

class ProcessManager
{
    /**
     * @var int
     */
    private $pid;

    /**
     * @var Queue[]
     */
    private $queues    = [];

    /**
     * @var Process[]
     */
    private $processes = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($title, LoggerInterface $logger = null)
    {
        $this->pid = getmypid();
        $this->setTitle($title);
        $this->logger = $logger ?: new SimpleLogger();
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

    /**
     * @param string $name
     *
     * @return Queue
     */
    public function getQueue($name)
    {
        return $this->queues[$name];
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    public function demonize()
    {
        if(($pid = pcntl_fork()) > 0) {
            exit(0);
        }
        elseif ($pid === -1) {
            throw new \RuntimeException('{Manager} Failure on demonize.');
        }

        return $this;
    }

    public function spawn(callable $job, $title = null, $id = null)
    {
        $process = new Process($this, $job, $title, $id);

        if (($pid = pcntl_fork()) === 0) {

            $this->setTitle($title);
            $process->setPid(getmypid());

            $this->getLogger()->debug(
                sprintf(
                    'New Child Process<%d> [%d] will run, Title: %s.',
                    $process->getPid(), $process->getId(), $title
                )
            );

            $process->run();
            exit(0);

        } elseif ($pid === -1) {
            throw new \RuntimeException('{Manager} Failure on spawn.');
        }

        $process->setPid($pid);
        $this->processes[$pid] = $process;

        return $pid;
    }

    public function wait()
    {
        while (($pid = pcntl_wait($status)) > 0) {

            $this->getLogger()->debug(
                sprintf('Child Process<%d> exited, Status: %d.', $pid, $status)
            );

            // TODO whether check success exit or not
            if (isset($this->processes[$pid])) { // respawn a process
                $process = $this->processes[$pid];
                $this->spawn($process->getJob(), $process->getTitle(), $process->getId());
            }
            else {
                $this->getLogger()->warning(
                    sprintf('Child Process<%d> exited, Not in watching list, Status: %d.', $pid, $status)
                );
            }

        }

        $this->getLogger()->debug(
            sprintf('Manager Process<%d> waiting finished, Last Status: %d.', $this->getPid(), $status)
        );
    }

    protected function setTitle($title)
    {
        if(function_exists('cli_set_process_title')) {
            cli_set_process_title($title); //PHP >= 5.5.
        } else if(function_exists('setproctitle')) {
            setproctitle($title); //PECL proctitle
        }
    }
}