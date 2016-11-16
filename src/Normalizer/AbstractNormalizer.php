<?php

namespace Tnc\Service\EventDispatcher\Normalizer;

use Tnc\Service\EventDispatcher\Interfaces\Normalizer;
use Tnc\Service\EventDispatcher\Interfaces\Serializer;

abstract class AbstractNormalizer implements Normalizer
{
    /**
     * @var \Tnc\Service\EventDispatcher\Interfaces\Serializer
     */
    protected $serializer;

    /**
     * @param \Tnc\Service\EventDispatcher\Interfaces\Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }
}
