<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Worker extends Process
{
    protected function run()
    {
        Utils::setProcessTitle('event-dispatcher worker');
        printf("Worker [%d] running, Parent [%d].\n", $this->getPid(), $this->getMaster()->getPid());

        while(true === msg_receive($this->getQueue(), 0, $msgType, 10000, $message, true, 0, $errorCode))
        {
            printf(
                "Worker [%s] has received a message, MsgType: %s, Message: %s, ErrorCode: %d\n",
                $this->getPid(), $msgType, $message, $errorCode
            );
            usleep(100000);
        }

        sleep(5);
    }
}