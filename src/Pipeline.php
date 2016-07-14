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
     * @var ChannelDetective
     */
    private $channelDetective;

    /**
     * PersistentQueue constructor.
     *
     * @param Backend    $backend
     * @param Serializer $serializer
     */
    public function __construct(
        ExternalDispatcher $externalDispatcher,
        Backend $backend,
        Serializer $serializer,
        ChannelDetective $channelDetective
    )
    {
        $this->externalDispatcher = $externalDispatcher;
        $this->backend            = $backend;
        $this->backend->setEventDispatcher($externalDispatcher);
        $this->serializer       = $serializer;
        $this->channelDetective = $channelDetective;
    }

    /**
     * @param \Tnc\Service\EventDispatcher\EventWrapper $eventWrapper
     */
    public function push(EventWrapper $eventWrapper)
    {
        try {
            $key     = $eventWrapper->getEvent()->getGroup();
            $channel = $this->channelDetective->getPushingChannel($eventWrapper);
            $message = $this->serializer->serialize($eventWrapper);

            $this->backend->push($channel, $message, $key);
        }
        catch (\Exception $e) {
            $this->externalDispatcher->dispatch(
                ErrorEvent::NAME,
                new ErrorEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::push}')
            );
        }
    }

    /**
     * @param int         $timeout
     * @param string|null $channel If null, will get default listening channel from ChannelDetective
     *
     * @return array [EventWrapper $eventWrapper, mixed $receipt]
     *
     * @throws \Tnc\Service\EventDispatcher\Exception\FatalException
     * @throws \Tnc\Service\EventDispatcher\Exception\TimeoutException
     * @throws \Tnc\Service\EventDispatcher\Exception\NoDataException
     */
    public function pop($timeout = 5000, $channel = null)
    {
        try {
            $channel = ($channel === null) ? $this->channelDetective->getDefaultPoppingChannel() : $channel;

            list($message, $receipt) = $this->backend->pop($channel, $timeout);

            $eventWrapper = null;
            if ($message) {
                $eventWrapper = $this->serializer->unserialize($message, EventWrapper::class);
            }

            return array($eventWrapper, $receipt);
        }
        catch (FatalException $e) {
            $this->externalDispatcher->dispatch(
                ErrorEvent::NAME,
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
        }
        catch (\Exception $e) {
            $this->externalDispatcher->dispatch(
                ErrorEvent::NAME,
                new ErrorEvent($e->getCode(), $e->getMessage(), '{PersistentPipeline::ack}')
            );
        }
    }
}