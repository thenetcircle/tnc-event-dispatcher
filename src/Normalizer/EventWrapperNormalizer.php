<?php

namespace Tnc\Service\EventDispatcher\Normalizer;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Event\DefaultEvent;
use Tnc\Service\EventDispatcher\EventWrapper;

class EventWrapperNormalizer extends AbstractNormalizer
{
    /**
     * @var string
     */
    private $wrapperField;

    public function __construct($wrapperField = DefaultEvent::EXTRA_FIELD)
    {
        $this->wrapperField = $wrapperField;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object)
    {
        /** @var EventWrapper $object */
        $data                               = $this->serializer->normalize($object->getEvent());
        $data[$this->wrapperField]['class'] = $object->getClass();
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class)
    {
        $temp       = $data[$this->wrapperField]['class'];
        $eventClass = (!empty($temp) && class_exists($temp)) ? $temp : DefaultEvent::class;

        /** @var Event $event */
        $event      = $this->serializer->denormalize($data, $eventClass);

        return new EventWrapper($event);
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
