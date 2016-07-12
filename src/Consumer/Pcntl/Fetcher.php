<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Fetcher extends Process
{
    protected function run()
    {
        Utils::setProcessTitle('event-dispatcher fetcher');
        printf("Fetcher [%d] running, Parent [%d].\n", $this->getPid(), $this->getMaster()->getPid());

        $i = mt_rand(1, 100);
        while(true === msg_send($this->getQueue(), 1, 'payload' . ++$i, true, true, $errorCode))
        {
            printf(
                "Fetcher [%d] Have sent a message: %s, ErrorCode: %d\n",
                $this->getPid(), 'payload' . $i, $errorCode
            );
            sleep(3);
        }

        sleep(10);
    }
}