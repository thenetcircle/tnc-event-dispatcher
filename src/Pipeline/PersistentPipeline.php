<?php

namespace Tnc\Service\EventDispatcher\Pipeline;

use Tnc\Service\EventDispatcher\Serializer;
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
     * @var int
     */
    private $timeout;

    /**
     * PersistentQueue constructor.
     *
     * @param string     $name
     * @param Driver     $driver
     * @param Serializer $serializer
     * @param int        $timeout
     */
    public function __construct($name, Driver $driver, Serializer $serializer, $timeout = 200)
    {
        $this->name       = $name;
        $this->driver     = $driver;
        $this->serializer = $serializer;
        $this->timeout    = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function push(WrappedEvent $wrappedEvent)
    {
        return $this->driver->push(
            $this->getChannel($wrappedEvent),
            $this->serializer->serialize($wrappedEvent),
            $this->timeout,
            $wrappedEvent->getMode()
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
    public function ack(WrappedEvent $wrappedEvent)
    {
        // TODO: Implement ack() method.
    }

    private function getChannel(WrappedEvent $wrappedEvent)
    {
        $name = $wrappedEvent->getName();
        if (($pos = strpos($name, '.')) !== false) {
            return substr($name, 0, $pos);
        } else {
            return $name;
        }
    }
}