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
    private $wrapperName;

    public function __construct($wrapperName = 'extra')
    {
        $this->wrapperName = $wrapperName;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object)
    {
        /** @var EventWrapper $object */
        $data                              = $this->serializer->normalize($object->getEvent());
        $data[$this->wrapperName]['class'] = $object->getClass();
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class)
    {
        $temp       = $data[$this->wrapperName]['class'];
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
