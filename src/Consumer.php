<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher;

class Consumer
{
    /**
     * @var callable
     */
    private $job;

    /**
     * @var int
     */
    private $workerNum;

    /**
     * @var string
     */
    private $processTitle;

    /**
     * Consumer constructor.
     *
     * @param callable $job The callable will accept one parameter (\Ko\Process $process)
     * @param int      $workerNum
     */
    public function __construct(callable $job, $workerNum = 1, $processTitle = 'EventDispatcher')
    {
        $this->job = $job;
        $this->workerNum = $workerNum;
        $this->processTitle = $processTitle;
    }

    /**
     * @return callable
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return int
     */
    public function getWorkerNum()
    {
        return $this->workerNum;
    }

    /**
     * @return string
     */
    public function getProcessTitle()
    {
        return $this->processTitle;
    }

    /**
     * Runs Consumer
     */
    public function run()
    {
        $consumer = $this;
        $manager = new \Ko\ProcessManager();
        $manager->demonize();
        $manager->setProcessTitle($this->getProcessTitle() . ':Master');
        for ($i = 0; $i < $this->workerNum; $i++) {
            $manager->spawn(
                function (\Ko\Process $process) use ($consumer) {
                    $process->setProcessTitle($consumer->getProcessTitle() . ':Worker');
                    call_user_func($consumer->getJob(), $process);
                }
            );
        }
        $manager->wait();
    }
}