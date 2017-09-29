<?php

namespace TNC\EventDispatcher\Serialization\Normalizer;

use \TNC\EventDispatcher\Serializer;

abstract class AbstractNormalizer implements Normalizer
{
    /**
     * @var \TNC\EventDispatcher\Serializer
     */
    protected $serializer;

    /**
     * @param \TNC\EventDispatcher\Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }
}