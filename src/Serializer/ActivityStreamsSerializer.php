<?php

namespace TNC\EventDispatcher\Serializer;

use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Normalizer\ActivityStreamsNormalizer;
use TNC\EventDispatcher\Normalizer\CustomNormalizer;
use TNC\EventDispatcher\Serializer\Encoder\JsonEncoder;

/**
 * ActivityStreamsSerializer
 */
class ActivityStreamsSerializer extends AbstractSerializer
{
    public function __construct()
    {
        parent::__construct(
          [
            new ActivityStreamsNormalizer(),
            new CustomNormalizer()
          ],
          new JsonEncoder()
        );
    }
}
