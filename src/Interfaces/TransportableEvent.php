<?php

namespace TNC\EventDispatcher\Interfaces;

interface TransportableEvent
{
    CONST TRANSPORT_MODE_SYNC      = 'sync';
    CONST TRANSPORT_MODE_SYNC_PLUS = 'sync_plus';
    CONST TRANSPORT_MODE_ASYNC     = 'async';

    public function getTransportMode();
}