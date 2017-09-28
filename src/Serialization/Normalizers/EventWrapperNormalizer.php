<?php

namespace TNC\EventDispatcher\Serialization\Normalizer;

use TNC\EventDispatcher\Interfaces\SerializableEvent;
use TNC\EventDispatcher\Event\DefaultEvent;
use TNC\EventDispatcher\Event\EventWrapper;
use \TNC\EventDispatcher\Interfaces\Serializer;

class EventWrapperNormalizerInterface implements NormalizerInterface
{
    const EXTRA_FIELD = 'extra';

    /**
     * @var \TNC\EventDispatcher\Interfaces\Serializer
     */
    protected $serializer;

    /**
     * @var string
     */
    private $extraField;

    public function __construct($extraField = self::EXTRA_FIELD)
    {
        $this->extraField = $extraField;
    }

    /**
     * @param \TNC\EventDispatcher\Interfaces\Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object)
    {
        /** @var EventWrapper $object */
        $data                             = $this->serializer->normalize($object->getEvent());
        $data[$this->extraField]['name']  = $object->getName();
        $data[$this->extraField]['mode']  = $object->getMode();
        $data[$this->extraField]['class'] = $object->getClass();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class)
    {
        $class      = $data[$this->extraField]['class'];
        $eventClass = (!empty($class) && class_exists($class)) ? $class : DefaultEvent::class;

        $name      = $data[$this->extraField]['name'];
        $mode      = $data[$this->extraField]['mode'];
        unset($data[$this->extraField]);

        /** @var SerializableEvent $event */
        $event      = $this->serializer->denormalize($data, $eventClass);

        return new EventWrapper($name, $event, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($object)
    {
        return ($object instanceof EventWrapper);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $class)
    {
        if (!class_exists($class)) {
            return false;
        }

        return $class == EventWrapper::class;
    }
}
