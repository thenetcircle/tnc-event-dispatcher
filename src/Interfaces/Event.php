<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Interfaces;

interface Event
{
    /**
     * Same transport-token events will be make sure in
     * order of event dispatching when transporting
     *
     * for non-transport events, it can returns anything and will be ignored
     *
     * @return string
     */
    public function getTransportToken();
}