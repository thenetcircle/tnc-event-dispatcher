<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Interfaces;

use TNC\EventDispatcher\Exception\TransportableEvent;
use TNC\EventDispatcher\Exception\UnserializeFailedException;
use TNC\EventDispatcher\Exception\SerializeFailedException;

interface SerializableEvent extends TransportableEvent
{
    /**
     * Serializes Event to be String
     *
     * @param Serializer $serializer
     *
     * @return string
     *
     * @throws SerializeFailedException
     */
    public function serialize(Serializer $serializer);

    /**
     * Unserializes String to be Event
     *
     * @param Serializer $serializer
     * @param string     $data
     *
     * @throws UnserializeFailedException
     */
    public function unserialize(Serializer $serializer, $data);
}