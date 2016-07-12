<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Worker extends Process
{
    const MSG_MAX_RECEIVE_SIZE = 1000000;

    protected function run()
    {
        Utils::setProcessTitle('event-dispatcher worker');
        printf("Worker [%d] running, Parent [%d].\n", $this->getPid(), $this->getManager()->getPid());

        while(
            true === msg_receive(
                $this->getJobQueue(),
                $this->getId(),
                $msgType,
                self::MSG_MAX_RECEIVE_SIZE,
                $message,
                true,
                0,
                $errorCode
            )
        ) {
            /*printf(
                "Worker [%s] has received a message, MsgType: %s, Message: %s, ErrorCode: %d\n",
                $this->getPid(), $msgType, json_encode($message), $errorCode
            );*/

            if(true === msg_send(
                $this->getReceiptQueue(),
                $message['from'],
                $message['payload'],
                true,
                true,
                $errorCode
            )) {
                /*printf(
                    "Worker [%s] has sent a receipt to Fetcher [%d], Receipt: %s, ErrorCode: %d\n",
                    $this->getPid(), $message['from'], $message['payload'], $errorCode
                );*/
            }

            sleep(3);
        }

        sleep(5);
    }
}