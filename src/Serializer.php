<?php

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Serializer\Normalizable;

interface Serializer
{
    /**
     * @param Normalizable $object
     *
     * @return string
     */
    public function serialize(Normalizable $object);

    /**
     * @param string $content
     * @param string $class
     *
     * @return Normalizable
     *
     * @throws InvalidArgumentException
     */
    public function unserialize($content, $class);
}
