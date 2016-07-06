<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

interface Event
{
    /**
     * Gets the channel name which the event will be delivery to
     *
     * @return string
     */
    public function getChannel();

    /**
     * Gets the group of the event
     *
     * @return string|null
     */
    public function getGroup();

    /**
     * Gets the event name
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the event name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Returns whether further event listeners should be triggered.
     *
     * @see Symfony\Component\EventDispatcher\Event::stopPropagation()
     *
     * @return bool Whether propagation was already stopped for this event.
     */
    public function isPropagationStopped();

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * If multiple event listeners are connected to the same event, no
     * further event listener will be triggered once any trigger calls
     * stopPropagation().
     *
     * @see Symfony\Component\EventDispatcher\Event::stopPropagation()
     */
    public function stopPropagation();
}