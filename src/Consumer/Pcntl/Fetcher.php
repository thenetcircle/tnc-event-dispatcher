<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Fetcher extends Process
{
    const MSG_MAX_RECEIVE_SIZE = 1000000;

    protected function run()
    {
        Utils::setProcessTitle('event-dispatcher fetcher');
        printf("Fetcher [%d] running, Parent [%d].\n", $this->getPid(), $this->getManager()->getPid());

        $channel = 'event-abc';

        $i = mt_rand(1, 100);
        while ($this->assignJob(
            $channel,
            array('from'=>$this->getId(), 'payload'=>'payload' . ++$i)
        )) {
            printf(
                "Fetcher [%d] has sent a message: %s, ErrorCode: %d\n",
                $this->getPid(), 'payload' . $i, $errorCode
            );

            if (($message = $this->waitingReceipt())) {
                printf(
                    "Fetcher [%d] has received a receipt: %s, Msgtype: %d, ErrorCode: %d\n",
                    $this->getPid(), $message, $msgType, $errorCode
                );
            }
            sleep(3);
        }

        sleep(10);
    }

    protected function getWorkerId($channel)
    {
        return ((int)abs(crc32($channel)) % $this->getManager()->getWorkersNum()) + 1;
    }
}