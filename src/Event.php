<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Serializer\Normalizable;

interface Event extends Normalizable
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
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
     * @return string
     */
    public function getMessageChannel();

    /**
     * @return string|null
     */
    public function getMessageKey();
}