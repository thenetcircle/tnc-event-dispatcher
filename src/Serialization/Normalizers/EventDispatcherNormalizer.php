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

use TNC\EventDispatcher\Dispatcher;
use TNC\EventDispatcher\Exception\DenormalizeException;
use TNC\EventDispatcher\Exception\NormalizeException;
use TNC\EventDispatcher\Interfaces\TransportableEvent;
use TNC\EventDispatcher\WrappedEvent;

class EventDispatcherNormalizer extends AbstractNormalizer
{
    const EXTRA_FIELD = 'extra';

    /**
     * @var \TNC\EventDispatcher\Dispatcher
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $extraField;

    public function __construct(Dispatcher $dispatcher, $extraField = self::EXTRA_FIELD)
    {
        $this->dispatcher = $dispatcher;
        $this->extraField = $extraField;
    }

    /**
     * Normalizes the Object to be a semi-result, Then can be using for Formatter
     *
     * @param WrappedEvent $wrappedEvent
     *
     * @return array
     *
     * @throws NormalizeException
     */
    public function normalize($wrappedEvent)
    {
        $data                             = $this->serializer->normalize($wrappedEvent->getEvent());
        $data[$this->extraField]['name']  = $wrappedEvent->getEventName();
        $data[$this->extraField]['mode']  = $wrappedEvent->getTransportMode();
        $data[$this->extraField]['class'] = $wrappedEvent->getClassName();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $className)
    {
        $name      = $data[$this->extraField]['name'];

        // If there is no listeners, Does not continue
        if (!$this->dispatcher->hasListeners($name)) {

        }

        $mode      = isset($data[$this->extraField]['mode']) ?
            $data[$this->extraField]['mode'] : TransportableEvent::TRANSPORT_MODE_ASYNC;

        $className = isset($data[$this->extraField]['class']) ?
            $data[$this->extraField]['class'] : $this->dispatcher->getTransportableEventClassName($name);

        if (empty($className)) {
            throw new DenormalizeException(sprintf("No listeners listening on event %s.", $name));
        }

        unset($data[$this->extraField]);
        /** @var \TNC\EventDispatcher\Interfaces\TransportableEvent $event */
        $event     = $this->serializer->denormalize($data, $className);

        return new WrappedEvent($name, $event, $mode);
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
