<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tnc\Service\EventDispatcher\Exception\DefaultException;

class Dispatcher extends BaseEventDispatcher
{
    CONST MODE_SYNC  = 1;
    CONST MODE_ASYNC = 2;
    CONST MODE_BOTH  = 3;

    /**
     * @var Producer
     */
    private $producer;

    /**
     * Dispatcher constructor.
     *
     * @param Producer $producer
     */
    public function __construct($producer)
    {
        $this->producer = $producer;
    }

    /**
     * Dispatches an event to all listeners by synchronous or asynchronous way
     *
     * @param string                   $eventName
     * @param Event|null               $event
     * @param int                      $mode
     * @param NormalizerInterface|null $normalizer
     *
     * @return Event
     */
    public function dispatch(
        $eventName,
        BaseEvent $event = null,
        $mode = self::MODE_BOTH,
        NormalizerInterface $normalizer = null
    )
    {
        if ($event === null) {
            $event = new Event();
        }

        switch ($mode) {
            case self::MODE_SYNC:
                parent::dispatch($eventName, $event);
                break;

            case self::MODE_ASYNC:
                $message = new Message($eventName, $event, $mode);
                $this->producer->produce($message);
                break;

            case self::MODE_BOTH:
                parent::dispatch($eventName, $event);
                $message = new Message($eventName, $event, $mode);
                $this->producer->produce($message);
                break;

            default:
                throw new DefaultException('Unsupported dispatch type.');
        }

        return $event;
    }
}