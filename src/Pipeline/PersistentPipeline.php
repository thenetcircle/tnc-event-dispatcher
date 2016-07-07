<?php

namespace Tnc\Service\EventDispatcher\Pipeline;

use Tnc\Service\EventDispatcher\Backend;
use Tnc\Service\EventDispatcher\Internal\AbstractInternalEventProducer;
use Tnc\Service\EventDispatcher\Internal\Event\ErrorEvent;
use Tnc\Service\EventDispatcher\Internal\InternalEventProducer;
use Tnc\Service\EventDispatcher\LocalDispatcher;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\EventWrapper;
use Tnc\Service\EventDispatcher\Pipeline;

class PersistentPipeline extends AbstractInternalEventProducer implements Pipeline
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
     * @param Backend    $backend
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
        try {
            $message = $this->serializer->serialize($eventWrapper);

            $this->backend->push(
                $eventWrapper->getChannel(),
                $message,
                $eventWrapper->getKey()
            );
        }
        catch (\Exception $e) {
            $this->dispatch(
                ErrorEvent::NAME,
                new ErrorEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::push}')
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function pop($channel, $timeout = 5000)
    {
        list($message, $receipt) = $this->backend->pop($channel, $timeout);

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
        parent::setInternalEventDispatcher($dispatcher);

        if ($this->backend instanceof InternalEventProducer) {
            $this->backend->setInternalEventDispatcher($dispatcher);
        }
    }
}