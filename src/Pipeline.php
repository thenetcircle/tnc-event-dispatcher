<?php

namespace TNC\Service\EventDispatcher;

use TNC\Service\EventDispatcher\Event\EventWrapper;
use TNC\Service\EventDispatcher\Event\Internal\DeliveryEvent;
use TNC\Service\EventDispatcher\Event\Internal\InternalEventProducer;
use TNC\Service\EventDispatcher\Exception\FatalException;
use TNC\Service\EventDispatcher\Event\Internal\ErrorEvent;
use TNC\Service\EventDispatcher\Interfaces\Backend;
use TNC\Service\EventDispatcher\Interfaces\ChannelDetective;
use TNC\Service\EventDispatcher\Interfaces\Serializer;

class Pipeline extends InternalEventProducer
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
     * @var ChannelDetective
     */
    private $channelDetective;

    /**
     * PersistentQueue constructor.
     *
     * @param Backend    $backend
     * @param Serializer $serializer
     * @param ChannelDetective $channelDetective
     */
    public function __construct(Backend $backend, Serializer $serializer, ChannelDetective $channelDetective) {
        $this->backend          = $backend;
        $this->serializer       = $serializer;
        $this->channelDetective = $channelDetective;
    }

    /**
     * @param \TNC\Service\EventDispatcher\Event\EventWrapper $eventWrapper
     */
    public function push(EventWrapper $eventWrapper)
    {
        try {

            $message  = $this->serializer->serialize($eventWrapper);
            $channels = $this->channelDetective->getPushingChannels($eventWrapper);
            $key      = $eventWrapper->getTransportToken();

            if (empty($message)) {
                $this->dispatchInternalEvent(
                    DeliveryEvent::FAILED,
                    new DeliveryEvent(implode(',', $channels), serialize($eventWrapper), $key)
                );
                return;
            }

            $this->backend->push($channels, $message, $key);

            $this->dispatchInternalEvent(
                DeliveryEvent::SUCCEED,
                new DeliveryEvent(implode(',', $channels), $message, $key)
            );

        } catch (\Exception $e) {
            $this->dispatchInternalEvent(
                ErrorEvent::ERROR,
                new ErrorEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::push}')
            );
        }
    }

    /**
     * @param int        $timeout  milliseconds
     * @param array|null $channels If null, will get default listening channels from ChannelDetective
     *
     * @return array [EventWrapper $eventWrapper, mixed $receipt]
     *
     * @throws \TNC\Service\EventDispatcher\Exception\FatalException
     * @throws \TNC\Service\EventDispatcher\Exception\TimeoutException
     * @throws \TNC\Service\EventDispatcher\Exception\NoDataException
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
                ErrorEvent::ERROR,
                new ErrorEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::pop}')
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
                ErrorEvent::ERROR,
                new ErrorEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::ack}')
            );
        }
    }

    /**
     * @return \TNC\Service\EventDispatcher\Interfaces\Backend
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * @return \TNC\Service\EventDispatcher\Interfaces\Serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @return \TNC\Service\EventDispatcher\Interfaces\ChannelDetective
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