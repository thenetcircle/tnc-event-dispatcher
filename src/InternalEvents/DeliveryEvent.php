<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Event\Internal;

use Symfony\Component\EventDispatcher\Event;

class DeliverySerializableEvent extends Event
{
    const SUCCEED = 'service.event-dispatcher.delivery.succeed';
    const FAILED  = 'service.event-dispatcher.delivery.failed';

    /**
     * @var string
     */
    protected $channel;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var int
     */
    protected $errCode;

    /**
     * DefaultEvent constructor.
     *
     * @param string $channel
     * @param string $message
     * @param string $key
     * @param int    $errCode
     */
    public function __construct($channel, $message, $key, $errCode = 0)
    {
        $this->channel = $channel;
        $this->message = $message;
        $this->key     = $key;
        $this->errCode = $errCode;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getErrCode()
    {
        return $this->errCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransportToken()
    {
        return '';
    }
}