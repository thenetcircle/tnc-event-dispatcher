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
use Tnc\Service\EventDispatcher\Exception\FatalException;

class Dispatcher extends BaseEventDispatcher
{
    CONST MODE_SYNC  = 1;
    CONST MODE_ASYNC = 2;
    CONST MODE_BOTH  = 3;

    /**
     * @var Pipeline
     */
    private $pipeline;

    /**
     * Dispatcher constructor.
     *
     * @param Pipeline $pipeline
     */
    public function __construct($pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * Dispatches an event to all listeners by synchronous or asynchronous way
     *
     * @param string     $eventName
     * @param Event|null $event
     * @param int        $mode
     *
     * @return Event
     */
    public function dispatch($eventName, BaseEvent $event = null, $mode = self::MODE_BOTH)
    {
        if ($event === null) {
            $event = new Event();
        }

        switch ($mode) {
            case self::MODE_SYNC:
                parent::dispatch($eventName, $event);
                break;

            case self::MODE_ASYNC:
                $this->pipeline->push(new WrappedEvent($eventName, $event, $mode));
                break;

            case self::MODE_BOTH:
                parent::dispatch($eventName, $event);
                $this->pipeline->push(new WrappedEvent($eventName, $event, $mode));
                break;

            default:
                throw new FatalException('Unsupported dispatch type.');
        }

        return $event;
    }
}