<?php

namespace Tnc\Service\EventDispatcher\Pipeline;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\LocalDispatcher;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\EventWrapper;
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
     * @var \SplObjectStorage
     */
    private $receipts;

    /**
     * PersistentQueue constructor.
     *
     * @param Driver     $driver
     * @param Serializer $serializer
     */
    public function __construct(Driver $driver, Serializer $serializer)
    {
        $this->driver         = $driver;
        $this->serializer     = $serializer;
        $this->receipts       = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function push(EventWrapper $eventWrapper)
    {
        $message = $this->serializer->serialize($eventWrapper);

        return $this->driver->push(
            $eventWrapper->getChannel(),
            $message,
            $eventWrapper->getGroup()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function pop($channel)
    {
        list($message, $receipt) = $this->driver->pop($channel);

        $eventWrapper = null;
        if ($message) {
            $eventWrapper = $this->serializer->unserialize($message, EventWrapper::class);
            $this->receipts->attach($eventWrapper, $receipt);
        }
        return $eventWrapper;
    }

    /**
     * {@inheritdoc}
     */
    public function ack(EventWrapper $eventWrapper)
    {
        if ($this->receipts->contains($eventWrapper)) {
            $this->driver->ack($this->receipts[$eventWrapper]);
            $this->receipts->detach($eventWrapper);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setInternalEventDispatcher(LocalDispatcher $dispatcher)
    {
        $this->driver->setInternalEventDispatcher($dispatcher);
    }
}