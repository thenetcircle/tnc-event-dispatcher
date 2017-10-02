<?php

namespace TNC\EventDispatcher\Serialization\Normalizers;

use TNC\EventDispatcher\Serialization\Normalizer;
use \TNC\EventDispatcher\Serializer;

abstract class AbstractNormalizer implements Normalizer
{
    /**
     * @var \TNC\EventDispatcher\Serializer
     */
    protected $serializer = null;

    /**
     * @param \TNC\EventDispatcher\Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }
}