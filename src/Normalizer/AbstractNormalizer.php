<?php

namespace TNC\Service\EventDispatcher\Normalizer;

use TNC\Service\EventDispatcher\Interfaces\Normalizer;
use TNC\Service\EventDispatcher\Interfaces\Serializer;

abstract class AbstractNormalizer implements Normalizer
{
    /**
     * @var \TNC\Service\EventDispatcher\Interfaces\Serializer
     */
    protected $serializer;

    /**
     * @param \TNC\Service\EventDispatcher\Interfaces\Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }
}
