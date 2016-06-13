<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentsException;

class Serializer
{
    /**
     * @param Serializable $serializable
     *
     * @return string
     */
    public function serialize(Serializable $serializable)
    {
        return $serializable->serialize($this);
    }

    /**
     * @param string $class
     * @param string $data
     *
     * @return Serializable
     *
     * @throws InvalidArgumentsException
     */
    public function unserialize($class, $data)
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentsException(sprintf('Class %s does not existed.', $class));
        }

        $reflectionClass = new \ReflectionClass($class);
        if (!$reflectionClass->isSubclassOf('Tnc\Service\EventDispatcher\Serializer\Serializable')) {
            throw new InvalidArgumentsException(sprintf('Class %s not serializable.', $class));
        }

        $object = $reflectionClass->newInstanceWithoutConstructor();
        $object->unserialize($data, $this);
        return $object;
    }
}
