<?php

namespace TNC\Service\EventDispatcher\Normalizer;

use TNC\Service\EventDispatcher\Exception\InvalidArgumentException;
use TNC\Service\EventDispatcher\Normalizer\ActivityStreams\Activity;
use TNC\Service\EventDispatcher\Normalizer\Interfaces\ActivityNormalizable;
use TNC\Service\EventDispatcher\Normalizer\Interfaces\ActivityDenormalizable;

class ActivityStreamsNormalizer extends AbstractNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object)
    {
        /** @var ActivityNormalizable $object */
        $activity = $object->normalizeActivity();
        return $this->serializer->normalize($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class)
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class %s does not existed.', $class));
        }

        $reflectionClass = new \ReflectionClass($class);
        if (!$reflectionClass->isSubclassOf(ActivityDenormalizable::class)) {
            throw new InvalidArgumentException(sprintf('Class %s not normalizable.', $class));
        }

        /** @var Activity $activity */
        $activity = $this->serializer->denormalize($data, Activity::class);

        /** @var ActivityDenormalizable $object */
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $object->denormalizeActivity($activity);
        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($object)
    {
        return ($object instanceof ActivityNormalizable);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $class)
    {
        if (!class_exists($class)) {
            return false;
        }

        return is_subclass_of($class, ActivityDenormalizable::class);
    }
}
