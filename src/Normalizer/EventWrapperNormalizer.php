<?php

namespace Tnc\Service\EventDispatcher\Normalizer;

use Tnc\Service\EventDispatcher\Interfaces\Event;
use Tnc\Service\EventDispatcher\Event\DefaultEvent;
use Tnc\Service\EventDispatcher\Event\EventWrapper;

class EventWrapperNormalizer extends AbstractNormalizer
{
    const EXTRA_FIELD = 'extra';

    /**
     * @var string
     */
    private $extraField;

    public function __construct($extraField = self::EXTRA_FIELD)
    {
        $this->extraField = $extraField;
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

        /** @var Event $event */
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
