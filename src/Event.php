<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

class Event extends BaseEvent implements Normalizable
{
    /**
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        return get_object_vars($this);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Serializer $serializer)
    {
        foreach ($data as $_key => $_value) {
            $this->{$_key} = $_value;
        }
    }
}