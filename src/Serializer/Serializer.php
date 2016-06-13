<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Tnc\Service\EventDispatcher\Exception\FatalException;

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
     * @throws FatalException
     */
    public function unserialize($class, $data)
    {
        if (!class_exists($class)) {
            throw new FatalException(sprintf('Class %s does not existed.', $class));
        }

        $reflectionObject = new \ReflectionClass($class);
        if (!$reflectionObject->isSubclassOf('Tnc\Service\EventDispatcher\Serializable')) {
            throw new FatalException(sprintf('Class %s not serializable.', $class));
        }

        $object = $reflectionObject->newInstanceWithoutConstructor();
        $object->unserialize($data, $this);
        return $object;
    }
}
