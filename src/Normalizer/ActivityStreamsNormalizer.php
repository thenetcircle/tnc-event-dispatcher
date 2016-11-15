<?php

namespace Tnc\Service\EventDispatcher\Normalizer;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Builder;
use Tnc\Service\EventDispatcher\Normalizer\Interfaces\ActivityStreamsNormalizable;
use Tnc\Service\EventDispatcher\Normalizer\Interfaces\ActivityStreamsDenormalizable;

class ActivityStreamsNormalizer extends AbstractNormalizer
{
    /**
     * @var Builder
     */
    protected $builder = null;

    public function __construct()
    {
        $this->builder = new Builder();
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object)
    {
        /** @var ActivityStreamsNormalizable $object */
        $object->normalize($this->builder);
        return $this->serializer->normalize($this->builder);
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
        if (!$reflectionClass->isSubclassOf(ActivityStreamsDenormalizable::class)) {
            throw new InvalidArgumentException(sprintf('Class %s not normalizable.', $class));
        }

        /** @var Builder $builder */
        $builder = $this->serializer->denormalize($data, Builder::class);

        /** @var ActivityStreamsDenormalizable $object */
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $object->denormalize($builder);
        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($object)
    {
        return ($object instanceof ActivityStreamsNormalizable);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $class)
    {
        if (!class_exists($class)) {
            return false;
        }

        return is_subclass_of($class, ActivityStreamsDenormalizable::class);
    }
}
