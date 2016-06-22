<?php

namespace Tnc\Service\EventDispatcher\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Tnc\Service\EventDispatcher\Dispatcher;
use Tnc\Service\EventDispatcher\Event\DefaultEvent;
use Tnc\Service\EventDispatcher\EventWrapper;

class EventWrapperNormalizer extends AbstractAggregateNormalizerAware implements
    NormalizerInterface,
    DenormalizerInterface
{
    CONST EXTRA_KEY = '_extra_';

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data                  = $this->aggregate->normalize($object->getEvent());
        $data[self::EXTRA_KEY] = [
            'class' => $object->getClass(),
            'mode'  => $object->getMode()
        ];
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $class = $mode = null;

        if (isset($data[self::EXTRA_KEY])) {
            $class = $data[self::EXTRA_KEY]['class'];
            $mode  = $data[self::EXTRA_KEY]['mode'];

            unset($data[self::EXTRA_KEY]);
        }

        $class = (!empty($class) && class_exists($class)) ? $class : DefaultEvent::class;
        $mode  = $mode ?: Dispatcher::MODE_ASYNC;
        $event = $this->aggregate->denormalize($data, $class);

        return new EventWrapper($event, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === EventWrapper::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EventWrapper;
    }
}
