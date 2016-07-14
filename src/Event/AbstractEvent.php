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
     * @var string
     */
    protected $group;

    /**
     * @var int
     */
    protected $mode;

    /**
     * @var bool Whether no further event listeners should be triggered
     */
    protected $propagationStopped = false;


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

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
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * {@inheritdoc}
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * {@inheritdoc}
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
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
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        return [
            'name'               => $this->getName(),
            'group'              => $this->getGroup(),
            'mode'               => $this->getMode(),
            'propagationStopped' => $this->propagationStopped
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(Serializer $serializer, array $data)
    {
        if(isset($data['name'])) {
            $this->name = $data['name'];
        }
        if(isset($data['group'])) {
            $this->group = $data['group'];
        }
        if(isset($data['mode'])) {
            $this->mode = $data['mode'];
        }
        if(isset($data['propagationStopped'])) {
            $this->propagationStopped = $data['propagationStopped'];
        }
    }
}