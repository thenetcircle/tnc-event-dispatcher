<?php

namespace TNC\EventDispatcher\Receivers;

use Psr\Http\Message\RequestInterface;

class HttpReceiver extends AbstractReceiver
{
    public function newRequest(RequestInterface $request) {
        $body =  $request->getBody()->getContents();
        // TODO: trace request processing
        $this->dispatcher->dispatchMessage($body);
    }
}