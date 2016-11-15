<?php

namespace Tnc\Service\EventDispatcher\Normalizer\Interfaces;

use Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Builder;

/**
 * ActivityStreamsDenormalizable
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
interface ActivityStreamsDenormalizable
{
    /**
     * Normalize instance to activity streams structure.
     *
     * @param \Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Builder $builder
     *
     * @throws \Tnc\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function denormalize(Builder $builder);
}
