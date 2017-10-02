<?php

namespace TNC\EventDispatcher\Serialization\Normalizer;

use TNC\EventDispatcher\Dispatcher;
use TNC\EventDispatcher\Exception\DenormalizeException;
use TNC\EventDispatcher\Exception\NormalizeException;
use TNC\EventDispatcher\Interfaces\TransportableEvent;
use TNC\EventDispatcher\WrappedEvent;

class EventDispatcherNormalizer extends AbstractNormalizer
{
    const EXTRA_FIELD = 'extra';

    /**
     * @var \TNC\EventDispatcher\Dispatcher
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $extraField;

    public function __construct(Dispatcher $dispatcher, $extraField = self::EXTRA_FIELD)
    {
        $this->dispatcher = $dispatcher;
        $this->extraField = $extraField;
    }

    /**
     * Normalizes the Object to be a semi-result, Then can be using for Formatter
     *
     * @param WrappedEvent $object
     *
     * @return array
     *
     * @throws NormalizeException
     */
    public function normalize($wrappedEvent)
    {
        $data                             = $this->serializer->normalize($wrappedEvent->getEvent());
        $data[$this->extraField]['name']  = $wrappedEvent->getEventName();
        $data[$this->extraField]['mode']  = $wrappedEvent->getTransportMode();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $className)
    {
        $name      = $data[$this->extraField]['name'];
        $mode      = $data[$this->extraField]['mode'] ?: TransportableEvent::TRANSPORT_MODE_ASYNC;
        unset($data[$this->extraField]);

        $className = $this->dispatcher->getTransportableEventClassName($name);
        if ($className === null) {
            throw new DenormalizeException(sprintf("No listeners listening on event %s.", $name));
        }

        /** @var \TNC\EventDispatcher\Interfaces\TransportableEvent $event */
        $event     = $this->serializer->denormalize($data, $className);

        return new WrappedEvent($name, $event, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($object)
    {
        return ($object instanceof WrappedEvent);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $className)
    {
        return $className == WrappedEvent::class;
    }
}
