<?php
/*
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Contributors:
 *     Beineng Ma <baineng.ma@gmail.com>
 */

namespace TNC\EventDispatcher\Receivers;

use Psr\Http\Message\RequestInterface;
use TNC\EventDispatcher\InternalEvents\InternalEvents;
use TNC\EventDispatcher\InternalEvents\ReceiverDispatchingFailedEvent;

class EventBusReceiver extends AbstractReceiver
{
    const SUCCESSFUL_RESPONSE = 'ok';
    const FAILED_RESPONSE     = 'ko';

    public function newRequest(RequestInterface $request)
    {
        $body =  $request->getBody()->getContents();

        try {
            $this->dispatchReceivedEvent($body);
            $this->dispatcher->dispatchSerializedEvent($body);
            return self::SUCCESSFUL_RESPONSE;
        }
        catch (\Exception $e) {
            if (null !== $this->dispatcher) {
                $this->dispatcher->dispatchInternalEvent(
                  InternalEvents::RECEIVER_DISPATCHING_FAILED,
                  new ReceiverDispatchingFailedEvent($body, $e)
                );
            }

            return self::FAILED_RESPONSE;
        }
    }
}