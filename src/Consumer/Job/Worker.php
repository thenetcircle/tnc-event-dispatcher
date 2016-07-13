<?php

namespace Tnc\Service\EventDispatcher\Consumer\Job;

use Tnc\Service\EventDispatcher\Consumer\Process;
use Tnc\Service\EventDispatcher\ExternalDispatcher;

final class Worker
{
    /**
     * @var ExternalDispatcher
     */
    private $externalDispatcher;

    public function __construct(ExternalDispatcher $externalDispatcher)
    {
        $this->externalDispatcher = $externalDispatcher;
    }

    public function __invoke(Process $process)
    {
        $process->getLogger()->debug(sprintf('Worker [%d] is running.', $process->getId()));

        $jobQueue     = $process->getQueue('job');
        $receiptQueue = $process->getQueue('receipt');
        $jobsNum = 0;

        while (false !== ($job = $jobQueue->pop($process->getId(), 3600000))) {
            list($fetcherId, $eventWrapper, $receipt) = $job;

            $process->getLogger()->debug(
                sprintf(
                    'Worker [%d] got a new job from Fetcher [%d]. EventWrapper: %s, Receipt: %s',
                    $process->getId(), $fetcherId, var_export($eventWrapper, true), var_export($receipt, true)
                )
            );

            $process->getLogger()->debug(
                sprintf('Worker [%d] got %d jobs.', $process->getId(), ++$jobsNum)
            );

            if ($receiptQueue->push($fetcherId, $receipt)) {
                $process->getLogger()->debug(
                    sprintf(
                        'Worker [%d] has pushed a receipt to Fetcher [%d].',
                        $process->getId(), $fetcherId
                    )
                );
            }
            else {
                $process->getLogger()->warning(
                    sprintf(
                        'Worker [%d] pushing a receipt to Fetcher [%d] failed. LastErrorCode: %d',
                        $process->getId(), $fetcherId, $receiptQueue->getLastErrorCode()
                    )
                );
            }
        }

        $process->getLogger()->warning(
            sprintf(
                'Worker [%d] will quit as there is no more jobs. LastErrorCode: %d',
                $process->getId(), $jobQueue->getLastErrorCode()
            )
        );
    }
}