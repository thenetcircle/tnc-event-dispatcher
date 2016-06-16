<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Normalizable;
use Tnc\Service\EventDispatcher\Serializer;

/**
 * AbstractSerializer
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
abstract class AbstractSerializer implements Serializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Normalizable $object)
    {
        return $this->encode(
            $this->normalize($object)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($class, $string)
    {
        return $this->denormalize(
            $class, $this->decode($string)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizable $object)
    {
        return $object->normalize($this);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($class, $data)
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class %s does not existed.', $class));
        }

        $reflectionClass = new \ReflectionClass($class);
        if (!$reflectionClass->isSubclassOf('Tnc\Service\EventDispatcher\Normalizable')) {
            throw new InvalidArgumentException(sprintf('Class %s not normalizable.', $class));
        }

        /** @var Normalizable $object */
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $object->denormalize($data, $this);
        return $object;
    }
}
