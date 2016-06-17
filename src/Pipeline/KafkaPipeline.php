<?php

namespace Tnc\Service\EventDispatcher\Pipeline;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\EventWrapper;
use Tnc\Service\EventDispatcher\Driver;
use Tnc\Service\EventDispatcher\Pipeline;

class KafkaPipeline implements Pipeline
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
    public function push(EventWrapper $eventWrapper, $timeout)
    {
        $key     = null;
        $channel = $this->getChannelPrefix() . 'default';

        if (null !== ($event = $eventWrapper->getEvent())) {
            $key     = $event->getGroup();
            $channel = $this->getChannelByEvent($event);
        }
        $message = $this->serializer->serialize($eventWrapper);

        return $this->driver->push($channel, $message, $timeout, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function pop($timeout)
    {
        $channel = '^' . preg_quote($this->getChannelPrefix()) . '.*';
        list($message, $receipt) = $this->driver->pop($channel, $timeout);

        $eventWrapper = null;
        if ($message) {
            $eventWrapper = $this->serializer->unserialize(EventWrapper::class, $message);
        }
        return $eventWrapper;
    }

    /**
     * {@inheritdoc}
     */
    public function ack(EventWrapper $eventWrapper)
    {
        $this->driver->ask(null);
    }


    /**
     * @return string
     */
    private function getChannelPrefix()
    {
        return 'event-';
    }

    /**
     * @param \Tnc\Service\EventDispatcher\Event $event
     *
     * @return string
     */
    private function getChannelByEvent(Event $event)
    {
        $name = $event->getName();
        if (($pos = strpos($name, '.')) !== false) {
            $channel = substr($name, 0, $pos);
        } else {
            $channel = $name;
        }

        return $this->getChannelPrefix() . $channel;
    }
}