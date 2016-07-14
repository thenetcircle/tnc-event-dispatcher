<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Event\DefaultEvent;
use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;

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
     * @var Event
     */
    private $defaultEvent;

    /**
     * Dispatcher constructor.
     *
     * @param ExternalDispatcher $externalDispatcher
     * @param Pipeline           $pipeline
     * @param Event              $defaultEvent
     */
    public function __construct(ExternalDispatcher $externalDispatcher, Pipeline $pipeline, Event $defaultEvent = null)
    {
        $this->externalDispatcher = $externalDispatcher;
        $this->pipeline           = $pipeline;
        $this->defaultEvent       = $defaultEvent === null ? new DefaultEvent() : $defaultEvent;
    }

    /**
     * Dispatches an event to all listeners by synchronous or asynchronous way
     *
     * @param string     $name
     * @param Event|null $event
     * @param string     $mode
     *
     * @return Event
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function dispatch($name, Event $event = null, $mode = self::MODE_SYNC_PLUS)
    {
        if ($event === null) {
            $event = $this->defaultEvent;
        }

        $event->setName($name);
        $event->setMode($mode);

        switch ($mode) {

            case self::MODE_SYNC:
                $this->externalDispatcher->dispatch($name, $event);
                break;

            case self::MODE_ASYNC:
                $this->pipeline->push(new EventWrapper($event));
                break;

            case self::MODE_SYNC_PLUS:
                $this->externalDispatcher->dispatch($name, $event);
                $this->pipeline->push(new EventWrapper($event));
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
}