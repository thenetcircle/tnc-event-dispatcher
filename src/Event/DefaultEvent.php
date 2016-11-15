<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Event;

use Tnc\Service\EventDispatcher\Normalizer\Interfaces\Denormalizable;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\Normalizer\Interfaces\Normalizable;

class DefaultEvent extends AbstractEvent implements Normalizable, Denormalizable, \ArrayAccess
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
    public function normalize(\Tnc\Service\EventDispatcher\Serializer $serializer)
    {
        $data = $this->data;
        $data['extra'] = [
            'name'               => $this->getName(),
            'group'              => $this->getGroup(),
            'mode'               => $this->getMode(),
            'propagationStopped' => $this->propagationStopped
        ];

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(\Tnc\Service\EventDispatcher\Serializer $serializer, array $data)
    {
        if (isset($data['extra'])) {
            $extra = $data['extra'];
            unset($data['extra']);

            if (isset($extra['name'])) {
                $this->name = $extra['name'];
            }
            if (isset($extra['group'])) {
                $this->group = $extra['group'];
            }
            if (isset($extra['mode'])) {
                $this->mode = $extra['mode'];
            }
            if (isset($extra['propagationStopped'])) {
                $this->propagationStopped = $extra['propagationStopped'];
            }
        }

        $this->data = $data;
    }
}