<?php

namespace Tnc\Service\EventDispatcher\Pipeline;

use Tnc\Service\EventDispatcher\Serializer\Serializer;
use Tnc\Service\EventDispatcher\WrappedEvent;
use Tnc\Service\EventDispatcher\Driver;
use Tnc\Service\EventDispatcher\Pipeline;

class PersistentPipeline implements Pipeline
{
    /**
     * @var string;
     */
    private $name;

    /**
     * @var Driver;
     */
    private $driver;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * PersistentQueue constructor.
     *
     * @param string     $name
     * @param Driver     $driver
     * @param Serializer $serializer
     * @param int        $timeout
     */
    public function __construct($name, Driver $driver, Serializer $serializer)
    {
        $this->name       = $name;
        $this->driver     = $driver;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function push(WrappedEvent $wrappedEvent, $timeout = 200)
    {
        return $this->driver->push(
            $this->getChannel($wrappedEvent),
            $this->serializer->serialize($wrappedEvent),
            $timeout,
            $wrappedEvent->getGroup()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function pop($timeout = 120000)
    {
        // TODO: Implement pop() method.
    }

    /**
     * {@inheritdoc}
     */
    public function ack(WrappedEvent $wrappedEvent)
    {
        // TODO: Implement ack() method.
    }

    /**
     * @param \Tnc\Service\EventDispatcher\WrappedEvent $wrappedEvent
     *
     * @return string
     */
    private function getChannel(WrappedEvent $wrappedEvent)
    {
        $name = $wrappedEvent->getName();
        if (($pos = strpos($name, '.')) !== false) {
            $channel = substr($name, 0, $pos);
        } else {
            $channel = $name;
        }

        $channel = 'event-' . $channel;
        return $channel;
    }
}