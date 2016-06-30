<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Internal\Event;

use Tnc\Service\EventDispatcher\Event\AbstractEvent;

class MessageEvent extends AbstractEvent
{
    const DELIVERY_FAILED = 'service.event-dispatcher.delivery.failed';

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
     * DefaultEvent constructor.
     *
     * @param string $channel
     * @param string $message
     * @param string $key
     */
    public function __construct($channel, $message, $key = null)
    {
        $this->channel = $channel;
        $this->message = $message;
        $this->key     = $key;
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
}