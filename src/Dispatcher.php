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
use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;

class Dispatcher extends BaseEventDispatcher
{
    CONST MODE_SYNC      = 'sync';
    CONST MODE_SYNC_PLUS = 'sync_plus';
    CONST MODE_ASYNC     = 'async';

    /**
     * @var string
     */
    private $source;
    /**
     * @var Pipeline
     */
    private $pipeline;
    /**
     * @var BaseEvent
     */
    private $defaultEvent;

    /**
     * Dispatcher constructor.
     *
     * @param Pipeline  $pipeline
     * @param string    $source
     * @param BaseEvent $defaultEvent
     */
    public function __construct(Pipeline $pipeline, $source = 'default')
    {
        $this->source   = $source;
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
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function dispatch($eventName, BaseEvent $event = null, $mode = self::MODE_SYNC_PLUS)
    {
        if ($event === null) {
            $event = $this->getDefaultEvent();
        }

        if (!$event instanceof Event) {
            throw new InvalidArgumentException(
                '{Dispatcher} Event is not an instance of Tnc\Service\EventDispatcher\Event.'
            );
        }

        $event->setDispatchingInfo($this->source, $eventName, $mode, time());

        switch ($mode) {

            case self::MODE_SYNC:
                parent::dispatch($eventName, $event);
                break;

            case self::MODE_ASYNC:
                $this->pipeline->push(new EventWrapper($event));
                break;

            case self::MODE_SYNC_PLUS:
                parent::dispatch($eventName, $event);
                $this->pipeline->push(new EventWrapper($event));
                break;

            default:
                throw new InvalidArgumentException('{Dispatcher} Unsupported dispatch mode.');

        }

        return $event;
    }

    /**
     * @return BaseEvent
     */
    public function getDefaultEvent()
    {
        return $this->defaultEvent ?: new Event();
    }

    /**
     * @param BaseEvent $event
     */
    public function setDefaultEvent($event)
    {
        $this->defaultEvent = $event;
    }
}