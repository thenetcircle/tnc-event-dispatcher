<?php

namespace Tnc\Service\EventDispatcher\Pipeline;

use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\WrappedEvent;
use Tnc\Service\EventDispatcher\Driver;
use Tnc\Service\EventDispatcher\Pipeline;

class PersistentPipeline implements Pipeline
{
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
     * @param Driver     $driver
     * @param Serializer $serializer
     * @param int        $timeout
     */
    public function __construct(Driver $driver, Serializer $serializer)
    {
        $this->driver     = $driver;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function push($channel, WrappedEvent $wrappedEvent, $timeout)
    {
        return $this->driver->push(
            $channel,
            $this->serializer->serialize($wrappedEvent),
            $timeout,
            $wrappedEvent->getGroup()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function pop($channel, $timeout)
    {
        list($message, $receipt) = $this->driver->pop($channel, $timeout);

        $wrappedEvent = null;
        if($message) {
            $wrappedEvent = $this->serializer->unserialize(
                WrappedEvent::class,
                $message
            );
        }

        return [$wrappedEvent, $receipt];
    }

    /**
     * {@inheritdoc}
     */
    public function ack($receipt)
    {
        return $this->driver->ack($receipt);
    }

    private function getSubscribedChannel()
    {
        return '^event-.*';
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