<?php

namespace Tnc\Service\EventDispatcher\Normalizer\Event;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Tnc\Service\EventDispatcher\Event\DefaultEvent;

class DefaultEventNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->all();
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $reflectionClass = new \ReflectionClass($class);
        $instance = $reflectionClass->newInstanceWithoutConstructor();
        $instance->setAll($data);
        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof DefaultEvent;
    }
}
