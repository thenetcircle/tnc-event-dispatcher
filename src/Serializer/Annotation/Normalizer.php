<?php

namespace Tnc\Service\EventDispatcher\Serializer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Normalizer
{
    const NORMAL           = 'Normal';
    const ACTIVITY_STREAMS = 'ActivityStreams';

    /**
     * @Required
     * @var string
     */
    public $type = self::NORMAL;
}
