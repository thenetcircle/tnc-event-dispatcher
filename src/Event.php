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
     * Gets the event name
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the event name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Gets the group of the event
     *
     * @return string|null
     */
    public function getGroup();

    /**
     * Sets the group of the event
     *
     * @param string $group
     *
     * @return $this
     */
    public function setGroup($group);

    /**
     * Gets the dispatching mode of the event
     *
     * @return int
     */
    public function getMode();

    /**
     * Sets the dispatching mode of the event
     *
     * @param int $mode
     *
     * @return $this
     */
    public function setMode($mode);

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