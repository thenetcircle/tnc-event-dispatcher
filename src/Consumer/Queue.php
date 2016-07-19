<?php

namespace Tnc\Service\EventDispatcher\Consumer;

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

    /**
     * @param int   $channel
     * @param mixed $message
     *
     * @return bool
     */
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

    /**
     * @param int $channel
     * @param int $timeout milliseconds
     *
     * @return mixed
     */
    public function pop($channel, $timeout = 3000)
    {
        $expireTime = microtime(true) + ($timeout / 1000);

        while (
            false === ($result = msg_receive(
                $this->msgQueue,
                $channel,
                $msgType,
                self::MSG_MAX_RECEIVE_SIZE,
                $message,
                true,
                MSG_IPC_NOWAIT,
                $errorCode
            ))
            && $errorCode === MSG_ENOMSG
        ) {

            if (microtime(true) > $expireTime) {
                break;
            }

            usleep(50000);
        }

        $this->lastErrorCode = $errorCode;

        return $result === false ? false : $message;
    }

    /**
     * @return array
     */
    public function info()
    {
        return msg_stat_queue($this->msgQueue);
    }

    /**
     * @return int
     */
    public function length()
    {
        $info = $this->info();
        return $info['msg_qnum'];
    }

    /**
     * @return int
     */
    public function getLastErrorCode()
    {
        return $this->lastErrorCode;
    }

    public function __destruct()
    {
        msg_remove_queue($this->msgQueue);
    }
}