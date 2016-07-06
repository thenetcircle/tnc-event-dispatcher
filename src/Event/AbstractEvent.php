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
     * Returns the prefix string of channels
     *
     * @return string
     */
    public static function getChannelPrefix()
    {
        return 'event-';
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

        return self::getChannelPrefix() . $channel;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return null;
    }

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
}