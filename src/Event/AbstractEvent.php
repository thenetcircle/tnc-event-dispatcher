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
use Tnc\Service\EventDispatcher\Normalizer;

abstract class AbstractEvent implements Event
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var bool Whether no further event listeners should be triggered
     */
    private $propagationStopped = false;

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        $name = $this->getName();
        if (($pos = strpos($name, '.')) !== false) {
            $channel = substr($name, 0, $pos);
        } else {
            $channel = $name;
        }

        return $this->getChannelPrefix() . $channel;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * {@inheritdoc}
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }

    /**
     * Returns the prefix string of channels
     *
     * @return string
     */
    public function getChannelPrefix()
    {
        return 'event-';
    }

    /**
     * Gets the name of the event
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer)
    {
        return get_object_vars($this);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Normalizer $normalizer)
    {
        foreach ($data as $_key => $_value) {
            $this->{$_key} = $_value;
        }
    }
}