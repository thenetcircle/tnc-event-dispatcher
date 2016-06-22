<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Event;

use ArrayAccess;
use Tnc\Service\EventDispatcher\Normalizer;

class DefaultEvent extends AbstractEvent implements ArrayAccess
{
    /**
     * @var array
     */
    protected $all = array();

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
        return array_key_exists($offset, $this->all);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->all[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
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
    public function normalize(Normalizer $normalizer)
    {
        return $this->all;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Normalizer $normalizer)
    {
        $this->all = $data;
    }
}