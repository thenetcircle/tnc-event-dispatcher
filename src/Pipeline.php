<?php

namespace TNC\EventDispatcher;

use TNC\EventDispatcher\Event\EventWrapper;
use TNC\EventDispatcher\Event\Internal\DeliverySerializableEvent;
use TNC\EventDispatcher\Event\Internal\InternalEventProducer;
use TNC\EventDispatcher\Exception\FatalException;
use TNC\EventDispatcher\Event\Internal\ErrorSerializableEvent;
use TNC\EventDispatcher\Interfaces\EndPoint;
use TNC\EventDispatcher\Interfaces\ChannelResolver;
use TNC\EventDispatcher\Interfaces\Serializer;

class Pipeline extends InternalEventProducer
{
    /**
     * @var EndPoint;
     */
    private $backend;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ChannelResolver
     */
    private $channelDetective;

    /**
     * PersistentQueue constructor.
     *
     * @param EndPoint        $backend
     * @param Serializer      $serializer
     * @param ChannelResolver $channelDetective
     */
    public function __construct(EndPoint $backend, Serializer $serializer, ChannelResolver $channelDetective) {
        $this->backend          = $backend;
        $this->serializer       = $serializer;
        $this->channelDetective = $channelDetective;
    }

    /**
     * @param \TNC\EventDispatcher\Event\EventWrapper $eventWrapper
     */
    public function push(EventWrapper $eventWrapper)
    {
        try {

            $message  = $this->serializer->serialize($eventWrapper);
            $channels = $this->channelDetective->getPushingChannels($eventWrapper);
            $key      = $eventWrapper->getTransportToken();

            if (empty($message)) {
                $this->dispatchInternalEvent(
                    DeliverySerializableEvent::FAILED,
                    new DeliverySerializableEvent(implode(',', $channels), serialize($eventWrapper), $key)
                );
                return;
            }

            $this->backend->send($channels, $message, $key);

            $this->dispatchInternalEvent(
                DeliverySerializableEvent::SUCCEED,
                new DeliverySerializableEvent(implode(',', $channels), $message, $key)
            );

        } catch (\Exception $e) {
            $this->dispatchInternalEvent(
                ErrorSerializableEvent::ERROR,
                new ErrorSerializableEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::push}')
            );
        }
    }

    /**
     * @param int        $timeout  milliseconds
     * @param array|null $channels If null, will get default listening channels from ChannelDetective
     *
     * @return array [EventWrapper $eventWrapper, mixed $receipt]
     *
     * @throws \TNC\EventDispatcher\Exception\FatalException
     * @throws \TNC\EventDispatcher\Exception\TimeoutException
     * @throws \TNC\EventDispatcher\Exception\NoDataException
     */
    public function pop($timeout = 5000, $channels = null)
    {
        try {
            $channels = ($channels === null) ? $this->channelDetective->getListeningChannels() : $channels;

            list($message, $receipt) = $this->backend->pop($channels, $timeout);

            $eventWrapper = null;
            if (!empty($message)) {
                $eventWrapper = $this->serializer->unserialize($message, EventWrapper::class);
            }

            return array($eventWrapper, $receipt);
        } catch (FatalException $e) {
            $this->dispatchInternalEvent(
                ErrorSerializableEvent::ERROR,
                new ErrorSerializableEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::pop}')
            );

            throw $e;
        }
    }

    /**
     * @param mixed $receipt
     */
    public function ack($receipt)
    {
        try {
            $this->backend->ack($receipt);
        } catch (\Exception $e) {
            $this->dispatchInternalEvent(
                ErrorSerializableEvent::ERROR,
                new ErrorSerializableEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::ack}')
            );
        }
    }

    /**
     * @return \TNC\EventDispatcher\Interfaces\EndPoint
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * @return \TNC\EventDispatcher\Interfaces\Serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @return \TNC\EventDispatcher\Interfaces\ChannelResolver
     */
    public function getChannelDetective()
    {
        return $this->channelDetective;
    }

    /**
     * {@inheritdoc}
     */
    public function setInternalEventDispatcher(Dispatcher $dispatcher)
    {
        $this->internalEventDispatcher = $dispatcher;

        if($this->backend instanceof InternalEventProducer) {
            $this->backend->setInternalEventDispatcher($dispatcher);
        }
    }
}