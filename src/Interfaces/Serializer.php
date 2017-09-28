<?php

namespace TNC\EventDispatcher\Interfaces;

use TNC\EventDispatcher\Exception\InvalidArgumentException;

interface Serializer
{
    /**
     * @param object $object
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function serialize($object);

    /**
     * @param string $data
     * @param string $class
     *
     * @return object
     *
     * @throws InvalidArgumentException
     */
    public function unserialize($data, $class);
}
