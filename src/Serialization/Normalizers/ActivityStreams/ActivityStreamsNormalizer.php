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
use TNC\EventDispatcher\Exception\NormalizeException;
use TNC\EventDispatcher\Interfaces\Event\ActivityStreamsEvent;
use TNC\EventDispatcher\Serialization\Normalizers\AbstractNormalizer;
use TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\Impl\Activity;
use TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\Impl\ActivityObject;

class ActivityStreamsNormalizer extends AbstractNormalizer
{
    /**
     * Normalizes the Object to be a semi-result, Then can be using for Formatter
     *
     * @param ActivityStreamsEvent $object
     *
     * @return array
     *
     * @throws NormalizeException
     */
    public function normalize($object)
    {
        $activity = $object->normalize(new ActivityBuilder());

        if (!is_object($activity) || !($activity instanceof Activity)) {
            throw new NormalizeException(sprintf(
              'The "normalize" method of class %s needs return a Activity Instance.', get_class($object)
            ));
        }

        return $this->normalizeActivity($activity->getAll());
    }

    /**
     * Denormalizes a semi-result to be a Object according to the $className
     *
     * @param array  $data
     * @param string $className
     *
     * @return ActivityStreamsEvent
     *
     * @throws DenormalizeException
     */
    public function denormalize($data, $className)
    {
        /** @var Activity $activity */
        $activity = $this->denormalizeActivity($data);

        /** @var ActivityStreamsEvent $object */
        $reflectionClass = new \ReflectionClass($className);
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $object->denormalize($activity);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($object)
    {
        return ($object instanceof ActivityStreamsEvent);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $className)
    {
        if (!class_exists($className)) {
            return false;
        }

        return is_subclass_of($className, ActivityStreamsEvent::class);
    }

    /**
     * @param array $properties
     *
     * @return array
     */
    protected function normalizeActivity(array $properties)
    {
        $data = [];

        foreach ($properties as $key => $value) {

            switch(true)
            {
                // mixed
                case $key == 'content':
                    if ($value == '') continue 2;
                    $value = \json_encode($value);
                    if ($value === false) continue 2;
                    $data[$key] = $value;
                    break;

                // string
                case in_array($key, ['version', 'id', 'title', 'url', 'verb', 'displayName', 'objectType', 'summary', 'image', 'icon']):
                    $value = (string)$value;
                    if (!is_string($value) || $value == '') continue 2;
                    $data[$key] = $value;
                    break;

                // object
                case in_array($key, ['actor', 'generator', 'object', 'provider', 'target', 'author']):
                    if (is_null($value) || !($value instanceof ActivityObject)) continue 2;
                    $_data = $this->normalizeActivity($value->getAll());
                    if (count($_data) > 0) {
                        $data[$key] = $_data;
                    }
                    break;

                // datetime
                case in_array($key, ['published', 'updated']):
                    if (!is_string($value) || $value == '') continue 2;
                    $data[$key] = $value;
                    break;

                // array of objects
                case $key == 'attachments':
                    if (!is_array($value) || count($value) === 0) continue 2;
                    $_data = [];
                    foreach($value as $atta) {
                        if (!is_object($atta) || !($atta instanceof ActivityObject)) continue;
                        $_subdata = $this->normalizeActivity($atta->getAll());
                        if (count($_subdata) > 0) {
                            $_data[] = $_subdata;
                        }
                    }
                    if (count($_data) > 0) {
                        $data[$key] = $_data;
                    }
                    break;

                // array of string
                case in_array($key, ['downstreamDuplicates', 'upstreamDuplicates']):
                    if (!is_array($value) || count($value) === 0) continue 2;
                    $data[$key] = $value;
                    break;
            }
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return \TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\Impl\Activity
     */
    protected function denormalizeActivity(array $data)
    {
        $activity = new Activity();

        foreach ($data as $key => $value) {

            switch(true)
            {
                // mixed
                case $key == 'content':
                    if (!is_string($value) || $value == '') continue 2;
                    if (null === ($_content = json_decode($value, true)))
                        $activity->setContent($value);
                    else
                        $activity->setContent($_content);
                    break;

                // activity object
                case in_array($key, ['actor', 'generator', 'object', 'provider', 'target']):
                    if (!is_array($value) || count($value) === 0) continue 2;
                    $method = 'set' . ucfirst($key);
                    $activity->{$method}($this->denormalizeActivityObject($value));
                    break;

                // string
                case in_array($key, ['icon', 'id', 'title', 'url', 'verb', 'published', 'updated']):
                    $method = 'set' . ucfirst($key);
                    $activity->{$method}($value);
                    break;
            }
        }

        return $activity;
    }

    /**
     * @param array $data
     *
     * @return \TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\Impl\ActivityObject
     */
    protected function denormalizeActivityObject(array $data)
    {
        $activityObject = new ActivityObject();

        foreach ($data as $key => $value) {

            switch(true)
            {
                // mixed
                case $key == 'content':
                    if (!is_string($value) || $value == '') continue 2;
                    if (null === ($_content = json_decode($value, true)))
                        $activityObject->setContent($value);
                    else
                        $activityObject->setContent($_content);
                    break;

                // object
                case $key == 'author':
                    if (!is_array($value)) continue 2;
                    $activityObject->setAuthor($this->denormalizeActivityObject($value));
                    break;

                // array of objects
                case $key == 'attachments':
                    if (!is_array($value) || count($value) === 0) continue 2;
                    foreach($value as $attaData) {
                        $activityObject->addAttachment($this->denormalizeActivityObject($attaData));
                    }
                    break;

                // string
                case in_array(
                  $key,
                  ['displayName', 'id',  'objectType',  'summary', 'url', 'published', 'updated', 'downstreamDuplicates', 'upstreamDuplicates', 'image']
                ):
                    $method = 'set' . ucfirst($key);
                    $activityObject->{$method}($value);
                    break;
            }
        }

        return $activityObject;
    }

}
