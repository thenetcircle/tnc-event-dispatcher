<?php

namespace Tnc\Service\EventDispatcher\Serializer\Annotation\ActivityStreams;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Actor
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $objectType;

    /**
     * @var array
     */
    public $context;
}
