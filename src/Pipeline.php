<?php

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Exception\FatalException;
use Tnc\Service\EventDispatcher\Event\Internal\ErrorEvent;

class Pipeline
{
    /**
     * @var ExternalDispatcher
     */
    private $externalDispatcher;

    /**
     * @var Backend;
     */
    private $backend;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * PersistentQueue constructor.
     *
     * @param Backend    $backend
     * @param Serializer $serializer
     */
    public function __construct(ExternalDispatcher $externalDispatcher, Backend $backend, Serializer $serializer)
    {
        $this->externalDispatcher = $externalDispatcher;
        $this->backend            = $backend;
        $this->serializer         = $serializer;
    }

    /**
     * @param \Tnc\Service\EventDispatcher\EventWrapper $eventWrapper
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
            $this->externalDispatcher->dispatch(
                ErrorEvent::NAME,
                new ErrorEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::push}')
            );
        }
    }

    /**
     * @param string $channel
     * @param int    $timeout
     *
     * @return array [EventWrapper $eventWrapper, mixed $receipt]
     *
     * @throws \Tnc\Service\EventDispatcher\Exception\FatalException
     * @throws \Tnc\Service\EventDispatcher\Exception\TimeoutException
     * @throws \Tnc\Service\EventDispatcher\Exception\NoDataException
     */
    public function pop($channel, $timeout = 5000)
    {
        try {
            list($message, $receipt) = $this->backend->pop($channel, $timeout);

            $eventWrapper = null;
            if ($message) {
                $eventWrapper = $this->serializer->unserialize($message, EventWrapper::class);
            }
        }
        catch (FatalException $e) {
            $this->externalDispatcher->dispatch(
                ErrorEvent::NAME,
                new ErrorEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::pop}')
            );

            throw $e;
        }

        return array($eventWrapper, $receipt);
    }

    /**
     * @param mixed $receipt
     */
    public function ack($receipt)
    {
        try {
            $this->backend->ack($receipt);
        }
        catch (\Exception $e) {
            $this->externalDispatcher->dispatch(
                ErrorEvent::NAME,
                new ErrorEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::ack}')
            );
        }
    }
}