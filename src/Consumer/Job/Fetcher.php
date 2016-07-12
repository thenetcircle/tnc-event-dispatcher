<?php

namespace Tnc\Service\EventDispatcher\Consumer\Job;

use Tnc\Service\EventDispatcher\Consumer\Process;

final class Fetcher
{
    /**
     * @var int
     */
    private $workersNum;

    public function __construct($workersNum)
    {
        $this->workersNum = $workersNum;
    }

    public function __invoke(Process $process)
    {
        printf("Fetcher [%d] running, Parent [%d].\n", $process->getPid(), $process->getManager()->getPid());

        $jobQueue     = $process->getQueue('job');
        $receiptQueue = $process->getQueue('receipt');

        $channel = 'event-message';
        $workerId = $this->getTargetWorkerId($channel);

        $i = mt_rand(1, 100);
        if(($payload = 'payload' . $i++) && $jobQueue->push(1, $payload)) {
            printf("Fetcher [%d] has sent a message: %s\n", $process->getPid(), $payload);

            if($receipt = $receiptQueue->pop($process->getId())) {
                printf("Fetcher [%d] has received a receipt: %s\n", $process->getPid(), $receipt);
            }
            else {
                printf("Fetcher [%d] receive message failed, Error: %s\n", $process->getPid(), $receiptQueue->getLastErrorCode
                ());
            }

            sleep(3);
        }

        printf('Fetcher [%d] push message failed. Error: %s', $process->getPid(),$jobQueue->getLastErrorCode());
        sleep(5);
    }

    protected function getTargetWorkerId($channel)
    {
        return ((int)abs(crc32($channel)) % $this->workersNum) + 1;
    }
}