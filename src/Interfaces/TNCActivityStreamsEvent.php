<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Interfaces;

use TNC\EventDispatcher\Exception\DenormalizeException;
use TNC\EventDispatcher\Exception\NormalizeException;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityBuilder;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity;

interface TNCActivityStreamsEvent extends TransportableEvent
{
    /**
     * Normalizes a Event to be a Activity
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityBuilder $builder
     *
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity
     *
     * @throws NormalizeException
     */
    public function normalize(TNCActivityBuilder $builder);

    /**
     * Denormalizes a Acitivty to be a Event
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity $activity
     *
     * @throws DenormalizeException
     */
    public function denormalize(Activity $activity);
}