<?php

namespace Tnc\Service\EventDispatcher\Consumer\Job;

use Tnc\Service\EventDispatcher\Consumer\Process;

final class Worker
{
    public function __invoke(Process $process)
    {
        printf("Worker [%d] running, Parent [%d].\n", $process->getPid(), $process->getManager()->getPid());

        $jobQueue     = $process->getQueue('job');
        $receiptQueue = $process->getQueue('receipt');

        if(false !== ($payload = $jobQueue->pop($process->getId()))) {
            printf("Worker [%d] has received a job: %s\n", $process->getPid(), $payload);


            sleep(3);

            $receipt = 'receipt_' . $payload;
            if($receiptQueue->push(1, $receipt)) {
                printf("Worker [%d] has sent a receipt: %s\n", $process->getPid(), $receipt);
            }
            else {
                printf("Worker [%d] sent message failed, Error: %s\n", $process->getPid(), $receiptQueue->getLastErrorCode());
            }
        }

        printf('Worker [%d] push message failed. Error: %s', $process->getPid(),$jobQueue->getLastErrorCode());
        sleep(5);
    }
}