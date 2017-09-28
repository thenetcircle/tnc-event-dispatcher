<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher;

use TNC\EventDispatcher\Event\DefaultEvent;
use TNC\EventDispatcher\Event\EventWrapper;
use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Interfaces\SerializableEvent;
use TNC\EventDispatcher\Interfaces\ExternalDispatcher;

class Dispatcher
{
    CONST MODE_SYNC      = 'sync';
    CONST MODE_SYNC_PLUS = 'sync_plus';
    CONST MODE_ASYNC     = 'async';

    /**
     * @var ExternalDispatcher
     */
    private $externalDispatcher;
    /**
     * @var Pipeline
     */
    private $pipeline;
    /**
     * @var SerializableEvent
     */
    private $defaultEvent;

    /**
     * Dispatcher constructor.
     *
     * @param ExternalDispatcher $externalDispatcher
     * @param Pipeline           $pipeline
     * @param SerializableEvent  $defaultEvent
     */
    public function __construct(ExternalDispatcher $externalDispatcher, Pipeline $pipeline, SerializableEvent $defaultEvent = null)
    {
        $this->externalDispatcher = $externalDispatcher;
        $this->pipeline           = $pipeline;
        $this->pipeline->setInternalEventDispatcher($this);
        $this->defaultEvent       = $defaultEvent === null ? new DefaultEvent() : $defaultEvent;
    }

    /**
     * Dispatches an event to all listeners by synchronous or asynchronous way
     *
     * @param string                 $name
     * @param SerializableEvent|null $event
     * @param string                 $mode
     *
     * @return SerializableEvent
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function dispatch($name, SerializableEvent $event = null, $mode = self::MODE_SYNC_PLUS)
    {
        if ($event === null) {
            $event = $this->defaultEvent;
        }

        switch ($mode) {

            case self::MODE_SYNC:
                $this->externalDispatcher->syncDispatch($name, $event);
                break;

            case self::MODE_ASYNC:
                $this->pipeline->push(new EventWrapper($name, $event, $mode));
                break;

            case self::MODE_SYNC_PLUS:
                $this->externalDispatcher->syncDispatch($name, $event);
                $this->pipeline->push(new EventWrapper($name, $event, $mode));
                break;

            default:
                throw new InvalidArgumentException('{Dispatcher} Unsupported dispatching mode.');

        }

        return $event;
    }

    /**
     * @return ExternalDispatcher
     */
    public function getExternalDispatcher()
    {
        return $this->externalDispatcher;
    }

    /**
     * @return Pipeline
     */
    public function getPipeline()
    {
        return $this->pipeline;
    }
}