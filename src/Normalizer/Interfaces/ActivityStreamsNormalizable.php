<?php

namespace Tnc\Service\EventDispatcher\Normalizer\Interfaces;

use Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Builder;

/**
 * ActivityStreamsNormalizable
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
interface ActivityStreamsNormalizable
{
    /**
     * Normalize instance to activity streams structure.
     *
     * @param \Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Builder $builder
     *
     * @throws \Tnc\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function normalize(Builder $builder);
}
