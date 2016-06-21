<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

interface Event extends Normalizable
{
    /**
     * @return string
     */
    public function getName();
    /**
     * @param string $name
     */
    public function setName($name);
}