<?php

namespace Tnc\Service\EventDispatcher\Serializer\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Accessor
{
    /**
     * @var string
     */
    public $getter;

    /**
     * @var string
     */
    public $setter;
}
