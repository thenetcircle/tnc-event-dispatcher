<?php

namespace Tnc\Service\EventDispatcher\Normalizer;

use Tnc\Service\EventDispatcher\Normalizer;
use Tnc\Service\EventDispatcher\Serializer;

/**
 * CustomNormalizer
 *
 * @package    Tnc\Service\EventDispatcher\Serializer
 *
 * @author     The NetCircle
 */
abstract class AbstractNormalizer implements Normalizer
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }
}
