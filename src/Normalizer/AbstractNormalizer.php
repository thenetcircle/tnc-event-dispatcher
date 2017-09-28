<?php

namespace TNC\EventDispatcher\Normalizer;

use TNC\EventDispatcher\Interfaces\Normalizer;
use TNC\EventDispatcher\Interfaces\Serializer;

abstract class AbstractNormalizer implements Normalizer
{
    /**
     * @var \TNC\EventDispatcher\Interfaces\Serializer
     */
    protected $serializer;

    /**
     * @param \TNC\EventDispatcher\Interfaces\Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }
}
