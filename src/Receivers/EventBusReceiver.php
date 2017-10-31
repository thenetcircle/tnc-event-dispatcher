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

class EventBusReceiver extends AbstractReceiver
{
    const SUCCESSFUL_RESPONSE = 'ok';
    const FAILED_RESPONSE     = 'ko';

    /**
     * Accepts new EventBus request, and dispatching to listeners
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * // TODO: tracing request, processing
     *
     * @return string
     */
    public function newRequest(RequestInterface $request)
    {
        $body = $request->getBody()->getContents();

        try {
            $this->dispatch($body);
            return self::SUCCESSFUL_RESPONSE;
        }
        catch (\Exception $e) {
            return self::FAILED_RESPONSE;
        }
    }
}