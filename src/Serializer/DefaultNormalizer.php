<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Normalizer;

/**
 * DefaultNormalizer
 *
 * @package    Tnc\Service\EventDispatcher\Serializer
 *
 * @author     The NetCircle
 */
class DefaultNormalizer implements Normalizer
{
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
    public function denormalize($data, $class)
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class %s does not existed.', $class));
        }

        $reflectionClass = new \ReflectionClass($class);
        if (!$reflectionClass->isSubclassOf('\Tnc\Service\EventDispatcher\Serializer\Normalizable')) {
            throw new InvalidArgumentException(sprintf('Class %s not normalizable.', $class));
        }

        /** @var Normalizable $object */
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $object->denormalize($data, $this);
        return $object;
    }
}
