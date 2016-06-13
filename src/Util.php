<?php

namespace Tnc\Service\EventDispatcher;

class Util
{
    /**
     * @param object $object
     * @param string $property
     * @param mixed  $value
     */
    public static function setInvisiblePropertyValue($object, $property, $value)
    {
        if (!is_object($object)) {
            return;
        }

        $property = new \ReflectionProperty($object, $property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}