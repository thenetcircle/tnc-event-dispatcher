<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

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
     * @param callable $job
     * @param int      $workerNum
     */
    public function __construct(callable $job, $workerNum = 1, $processTitle = 'EventDispatcher')
    {
        $this->job = $job;
        $this->workerNum = $workerNum;
        $this->processTitle = $processTitle;
    }

    /**
     * Runs Consumer
     */
    public function run()
    {
        $manager = new \Ko\ProcessManager();
        $manager->demonize();
        $manager->setProcessTitle($this->processTitle . ':Master');
        for ($i = 0; $i < $this->workerNum; $i++) {
            $manager->spawn(
                function (\Ko\Process $process) use ($this) {
                    $process->setProcessTitle($this->processTitle . ':Worker');
                    call_user_func($this->job);
                }
            );
        }
        $manager->wait();
    }
}