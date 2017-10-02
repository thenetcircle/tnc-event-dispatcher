<?php

namespace TNC\EventDispatcher\Serialization\Normalizer;

use TNC\EventDispatcher\Exception\DenormalizeException;
use TNC\EventDispatcher\WrappedEvent;

class WrappedEventNormalizer extends AbstractNormalizer
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
        /** @var WrappedEvent $object */
        $data                             = $this->serializer->normalize($object->getEvent());
        $data[$this->extraField]['name']  = $object->getEventName();
        $data[$this->extraField]['mode']  = $object->getTransportMode();
        $data[$this->extraField]['class'] = $object->getClassName();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $className)
    {
        $className  = $data[$this->extraField]['class'];

        if (empty($className) || !class_exists($className)) {
            throw new DenormalizeException(sprintf("Class %s does not exists.", $className));
        }

        $name      = $data[$this->extraField]['name'];
        $mode      = $data[$this->extraField]['mode'];
        unset($data[$this->extraField]);

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
        if (!class_exists($className)) {
            return false;
        }

        return $className == WrappedEvent::class;
    }
}
