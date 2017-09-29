<?php

namespace TNC\EventDispatcher\Serialization\Normalizer;

use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Serialization\Normalizer\Interfaces\Normalizable;
use TNC\EventDispatcher\Serialization\Normalizer\Interfaces\Denormalizable;
use \TNC\EventDispatcher\Interfaces\Serializer;

class DefaultNormalizer extends AbstractNormalizer
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
    public function denormalize($data, $className)
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Class %s does not existed.', $className));
        }

        $reflectionClass = new \ReflectionClass($className);
        if (!$reflectionClass->isSubclassOf(Denormalizable::class)) {
            throw new InvalidArgumentException(sprintf('Class %s not normalizable.', $className));
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
    public function supportsDenormalization($data, $className)
    {
        if (!class_exists($className)) {
            return false;
        }

        return is_subclass_of($className, Denormalizable::class);
    }
}
