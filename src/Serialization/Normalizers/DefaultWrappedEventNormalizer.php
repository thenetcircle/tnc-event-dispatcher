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

namespace TNC\EventDispatcher\Serialization\Normalizers;

use TNC\EventDispatcher\Interfaces\Event\TransportableEvent;
use TNC\EventDispatcher\WrappedEvent;

class DefaultWrappedEventNormalizer extends AbstractNormalizer
{
    const WRAPPER_FIELD = 'wrapper';

    /**
     * @var string
     */
    private $wrapperField;

    public function __construct($wrapperField = self::WRAPPER_FIELD)
    {
        $this->wrapperField = $wrapperField;
    }

    /**
     * {@inheritdoc}
     *
     * @param WrappedEvent $wrappedEvent
     */
    public function normalize($wrappedEvent)
    {
        $data                               = $wrappedEvent->getNormalizedEvent();
        $data[$this->wrapperField]['name']  = $wrappedEvent->getEventName();
        $data[$this->wrapperField]['mode']  = $wrappedEvent->getTransportMode();
        $data[$this->wrapperField]['class'] = $wrappedEvent->getClassName();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $className)
    {
        $eventName       = $data[$this->wrapperField]['name'];
        $transportMode   = isset($data[$this->wrapperField]['mode']) ?
            $data[$this->wrapperField]['mode'] : TransportableEvent::TRANSPORT_MODE_ASYNC;
        $eventClassName  = isset($data[$this->wrapperField]['class']) ? $data[$this->wrapperField]['class'] : '';
        unset($data[$this->wrapperField]);
        $normalizedEvent = $data;

        return new WrappedEvent($transportMode, $eventName, $normalizedEvent, $eventClassName);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($object)
    {
        return ($object instanceof WrappedEvent);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $className)
    {
        return $className == WrappedEvent::class;
    }
}
