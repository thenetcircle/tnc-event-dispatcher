<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Queue
{
    const MSG_MAX_RECEIVE_SIZE = 1000000;

    private static $keyPrefix = 1;

    private $name;

    private $msgQueue;

    private $lastErrorCode;

    public function __construct($name)
    {
        $this->name = $name;

        $key            = ftok(__FILE__, self::$keyPrefix++);
        $this->msgQueue = msg_get_queue($key, 0666);
    }

    public function getName()
    {
        return $this->name;
    }

    public function push($channel, $message)
    {
        $result = msg_send(
            $this->msgQueue,
            $channel,
            $message,
            true,
            true,
            $errorCode
        );

        $this->lastErrorCode = $errorCode;

        return $result;
    }

    public function pop($channel)
    {
        $result = msg_receive(
            $this->msgQueue,
            $channel,
            $msgType,
            self::MSG_MAX_RECEIVE_SIZE,
            $message,
            true,
            0,
            $errorCode
        );

        $this->lastErrorCode = $errorCode;

        return $message;
    }

    public function info()
    {
        return msg_stat_queue($this->msgQueue);
    }

    public function getLastErrorCode()
    {
        return $this->lastErrorCode;
    }

    public function __destruct()
    {
        msg_remove_queue($this->msgQueue);
    }
}