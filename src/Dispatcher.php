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
    private $domainId;
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
     * @param string    $domainId
     * @param Pipeline  $pipeline
     * @param BaseEvent $defaultEvent
     */
    public function __construct($domainId, Pipeline $pipeline, BaseEvent $defaultEvent = null)
    {
        $this->domainId     = $domainId;
        $this->pipeline     = $pipeline;
        $this->defaultEvent = $defaultEvent ?: new Event();
    }

    /**
     * Dispatches an event to all listeners by synchronous or asynchronous way
     *
     * @param string         $eventName
     * @param BaseEvent|null $event
     * @param int            $mode
     * @param string         $group Event who in same group will be ordered consuming
     *
     * @return Event
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function dispatch($eventName, BaseEvent $event = null, $mode = self::MODE_SYNC_PLUS, $group = null)
    {
        if ($event === null) {
            $event = $this->defaultEvent;
        }
        if ($group === null) {
            // $group = 'r' . mt_rand(); // random a group if it's null
        }

        switch ($mode) {

            case self::MODE_SYNC:
                parent::dispatch($eventName, $event);
                break;

            case self::MODE_ASYNC:
                $this->pipeline->push(new WrappedEvent($this->domainId, $eventName, $event, $group, $mode));
                break;

            case self::MODE_SYNC_PLUS:
                parent::dispatch($eventName, $event);
                $this->pipeline->push(new WrappedEvent($this->domainId, $eventName, $event, $group, $mode));
                break;

            default:
                throw new InvalidArgumentException('Unsupported dispatch mode.');

        }

        return $event;
    }
}