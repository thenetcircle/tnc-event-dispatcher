<?php

namespace Tnc\Service\EventDispatcher\Pipeline;

use Tnc\Service\EventDispatcher\Backend;
use Tnc\Service\EventDispatcher\Internal\InternalEventProducer;
use Tnc\Service\EventDispatcher\LocalDispatcher;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\EventWrapper;
use Tnc\Service\EventDispatcher\Pipeline;

class PersistentPipeline implements Pipeline, InternalEventProducer
{
    /**
     * @var Backend;
     */
    private $backend;

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
     * @param Backend     $backend
     * @param Serializer $serializer
     */
    public function __construct(Backend $backend, Serializer $serializer)
    {
        $this->backend    = $backend;
        $this->serializer = $serializer;
        $this->receipts   = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function push(EventWrapper $eventWrapper)
    {
        $message = $this->serializer->serialize($eventWrapper);

        return $this->backend->push(
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
        list($message, $receipt) = $this->backend->pop($channel);

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
            $this->backend->ack($this->receipts[$eventWrapper]);
            $this->receipts->detach($eventWrapper);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setInternalEventDispatcher(LocalDispatcher $dispatcher)
    {
        if($this->backend instanceof InternalEventProducer) {
            $this->backend->setInternalEventDispatcher($dispatcher);
        }
    }
}