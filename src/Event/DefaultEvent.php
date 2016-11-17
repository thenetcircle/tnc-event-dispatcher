<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\Service\EventDispatcher\Event;

use \ArrayAccess;
use TNC\Service\EventDispatcher\Interfaces\Event;
use TNC\Service\EventDispatcher\Normalizer\Interfaces\Denormalizable;
use TNC\Service\EventDispatcher\Normalizer\Interfaces\Normalizable;

class DefaultEvent implements Event, Normalizable, Denormalizable, ArrayAccess
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * DefaultEvent constructor.
     *
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->data = $data;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->offsetExists($name);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function __get($property)
    {
        return $this->offsetGet($property);
    }

    public function __set($property, $value)
    {
        $this->offsetSet($property, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransportToken()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(\TNC\Service\EventDispatcher\Interfaces\Serializer $serializer)
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(\TNC\Service\EventDispatcher\Interfaces\Serializer $serializer, array $data)
    {
        $this->data = $data;
    }
}