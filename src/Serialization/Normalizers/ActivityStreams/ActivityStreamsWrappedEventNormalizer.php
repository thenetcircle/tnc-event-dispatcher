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

namespace TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams;

use TNC\EventDispatcher\Exception\DenormalizeException;
use TNC\EventDispatcher\Interfaces\Event\TransportableEvent;
use TNC\EventDispatcher\Serialization\Normalizers\AbstractNormalizer;
use TNC\EventDispatcher\WrappedEvent;

class ActivityStreamsWrappedEventNormalizer extends AbstractNormalizer
{
    const CONTAINER_FIELD = 'generator';

    /**
     * @var string
     */
    protected $titlePrefix = '';

    public function __construct($options = [])
    {
        if (isset($options['title_prefix']) && !empty($options['title_prefix'])) {
            $this->titlePrefix = $options['title_prefix'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($wrappedEvent)
    {
        $data         = $wrappedEvent->getNormalizedEvent();

        $eventName = $wrappedEvent->getEventName();
        $data['title'] = $this->stripTitlePrefix($eventName);
        if (strpos($eventName, '.') !== false) {
            $data['verb'] = substr(strrchr($eventName, '.'), 1);
        }

        // generate a unique id if "id" is not set,
        // format: ED[[-ProviderId][-Title][-ActorId]]-RandomString
        if (!isset($data['id']) || $data['id'] == '') {
            $prefix = 'ED';
            if (isset($data['provider']['id'])) $prefix .= '-' . $data['provider']['id'];
            if (isset($data['title'])) $prefix .= '-' . $data['title'];
            if (isset($data['actor']['id'])) $prefix .= '-' . $data['actor']['id'];

            $data['id'] = uniqid($prefix . '-');
        }

        $data['generator'] = [
          'id'      => 'tnc-event-dispatcher',
          'objectType' => 'library',
          'content' => json_encode([
            'mode'  => $wrappedEvent->getTransportMode(),
            'class' => $wrappedEvent->getClassName()
          ])
        ];

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $className)
    {
        if (!isset($data['title'])) {
            throw new DenormalizeException('The field "title" is required.');
        }

        $eventName = $this->addTitlePrefix($data['title']);
        $metadata  = [];

        if (
          isset($data['generator']) &&
          is_array($data['generator']) &&
          $data['generator']['id'] == 'tnc-event-dispatcher' &&
          isset($data['generator']['content']) &&
          is_string($data['generator']['content'])
        ) {
            $metadata = json_decode($data['generator']['content'], true);
            if ($metadata === null) $metadata = [];
        }

        $transportMode = isset($metadata['mode']) ? $metadata['mode'] : TransportableEvent::TRANSPORT_MODE_ASYNC;
        $className     = isset($metadata['class']) ? $metadata['class'] : '';

        return new WrappedEvent($transportMode, $eventName, $data, $className);
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

    /**
     * @param $eventName
     *
     * @return string
     */
    private function stripTitlePrefix($eventName)
    {
        if (!empty($this->titlePrefix) && strpos($eventName, $this->titlePrefix) === 0) {
            return substr($eventName, strlen($this->titlePrefix));
        }
        return $eventName;
    }

    /**
     * @param $eventName
     *
     * @return string
     */
    private function addTitlePrefix($eventName)
    {
        if (!empty($this->titlePrefix) && strpos($eventName, $this->titlePrefix) !== 0) {
            return $this->titlePrefix . $eventName;
        }
        return $eventName;
    }
}
