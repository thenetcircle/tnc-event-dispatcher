<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Event;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Serializer;

abstract class AbstractEvent implements Event
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


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