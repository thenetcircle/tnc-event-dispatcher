<?php

namespace Tnc\Service\EventDispatcher\Pipeline;

use Tnc\Service\EventDispatcher\Event;
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
     * @var int
     */
    private $defaultTimeout;

    /**
     * @var \SplObjectStorage
     */
    private $receipts;

    /**
     * PersistentQueue constructor.
     *
     * @param Driver     $driver
     * @param Serializer $serializer
     * @param int        $defaultTimeout
     */
    public function __construct(Driver $driver, Serializer $serializer, $defaultTimeout = 200)
    {
        $this->driver         = $driver;
        $this->serializer     = $serializer;
        $this->defaultTimeout = $defaultTimeout;
        $this->receipts       = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function push(EventWrapper $eventWrapper, $timeout = null)
    {
        $timeout = $timeout === null ? $this->defaultTimeout : $timeout;
        $message = $this->serializer->serialize($eventWrapper);

        return $this->driver->push(
            $eventWrapper->getMessageChannel(),
            $message,
            $timeout,
            $eventWrapper->getMessageKey()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function pop($channel, $timeout = null)
    {
        $timeout = $timeout === null ? $this->defaultTimeout : $timeout;

        list($message, $receipt) = $this->driver->pop($channel, $timeout);

        $eventWrapper = null;
        if ($message) {
            $eventWrapper = $this->serializer->unserialize(EventWrapper::class, $message);
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
}