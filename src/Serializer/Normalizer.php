<?php

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Serializer\Normalizable;

interface Normalizer
{
    /**
     * @param Normalizable $object
     *
     * @return array
     */
    public function normalize(Normalizable $object);

    /**
     * @param array  $data
     * @param string $class
     *
     * @return Normalizable
     *
     * @throws InvalidArgumentException
    */
    public function denormalize($data, $class);
}
