<?php

namespace Tnc\Service\EventDispatcher\Pipeline;

use Symfony\Component\Serializer\Serializer;
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
     * @param string                                   $name
     * @param \Tnc\Service\EventDispatcher\Driver      $driver
     * @param \Symfony\Component\Serializer\Serializer $serializer
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
    public function push(WrappedEvent $event)
    {
        $channel = $event->getEventName();
        return $this->driver->push(
            $channel,
            $this->serializer->serialize($event),
            200,
            $event->getMode()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function pop()
    {
        // TODO: Implement pop() method.
    }

    /**
     * {@inheritdoc}
     */
    public function ack(WrappedEvent $event)
    {
        // TODO: Implement ack() method.
    }
}