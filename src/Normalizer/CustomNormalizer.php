<?php

namespace TNC\EventDispatcher\Normalizer;

use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Normalizer\Interfaces\Normalizable;
use TNC\EventDispatcher\Normalizer\Interfaces\Denormalizable;

class CustomNormalizer extends AbstractNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object)
    {
        /** @var Normalizable $object */
        return $object->normalize($this->serializer);
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
        if (!$reflectionClass->isSubclassOf(Denormalizable::class)) {
            throw new InvalidArgumentException(sprintf('Class %s not normalizable.', $class));
        }

        /** @var Denormalizable $object */
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $object->denormalize($this->serializer, $data);
        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($object)
    {
        return ($object instanceof Normalizable);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $class)
    {
        if (!class_exists($class)) {
            return false;
        }

        return is_subclass_of($class, Denormalizable::class);
    }
}
