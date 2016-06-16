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

class Event extends BaseEvent implements Serializable
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Serializer $serializer)
    {
        return json_encode(get_object_vars($this));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($string, Serializer $serializer)
    {
        if (null === ($data = json_decode($string, true))) {
            throw new InvalidArgumentException(sprintf('{%s} can not unserialize data %s', get_called_class(), $string));
        }
        foreach ($data as $_key => $_value) {
            $this->{$_key} = $_value;
        }
    }
}