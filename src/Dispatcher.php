<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Event\Activity;
use Tnc\Service\EventDispatcher\Event\ActivityEvent;
use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;

class Dispatcher
{
    CONST MODE_SYNC      = 'sync';
    CONST MODE_SYNC_PLUS = 'sync_plus';
    CONST MODE_ASYNC     = 'async';

    /**
     * @var LocalDispatcher
     */
    private $localDispatcher;
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
     * @param LocalDispatcher $localDispatcher
     * @param Pipeline        $pipeline
     * @param Event           $defaultEvent
     */
    public function __construct(LocalDispatcher $localDispatcher, Pipeline $pipeline, Event $defaultEvent = null)
    {
        $this->localDispatcher = $localDispatcher;
        $this->pipeline        = $pipeline;
        $this->defaultEvent    = $defaultEvent === null ? new ActivityEvent() : $defaultEvent;
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

        switch ($mode) {

            case self::MODE_SYNC:
                $this->localDispatcher->dispatch($name, $event);
                break;

            case self::MODE_ASYNC:
                $this->pipeline->push(new EventWrapper($event, $mode));
                break;

            case self::MODE_SYNC_PLUS:
                $this->localDispatcher->dispatch($name, $event);
                $this->pipeline->push(new EventWrapper($event, $mode));
                break;

            default:
                throw new InvalidArgumentException('{Dispatcher} Unsupported dispatch mode.');

        }

        return $event;
    }

    /**
     * @return LocalDispatcher
     */
    public function getLocalDispatcher()
    {
        return $this->localDispatcher;
    }
}