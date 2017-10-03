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

namespace TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams;

use TNC\EventDispatcher\Exceptions\DenormalizeException;
use TNC\EventDispatcher\Exceptions\NormalizeException;
use TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent;
use TNC\EventDispatcher\Serialization\Normalizers\AbstractNormalizer;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Actor;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Obj;

class TNCActivityStreamsNormalizer extends AbstractNormalizer
{
    /**
     * @var null|\TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityBuilder
     */
    protected $activityBuilder = null;

    public function __construct()
    {
        $this->activityBuilder = new TNCActivityBuilder();
    }

    /**
     * Normalizes the Object to be a semi-result, Then can be using for Formatter
     *
     * @param TNCActivityStreamsEvent $object
     *
     * @return array
     *
     * @throws NormalizeException
     */
    public function normalize($object)
    {
        $activity = $object->normalize($this->activityBuilder);

        return $this->normalizeActivity($activity);
    }

    /**
     * Denormalizes a semi-result to be a Object according to the $className
     *
     * @param array  $data
     * @param string $className
     *
     * @return TNCActivityStreamsEvent
     *
     * @throws DenormalizeException
     */
    public function denormalize($data, $className)
    {
        $reflectionClass = new \ReflectionClass($className);

        /** @var Activity $activity */
        $activity = $this->denormalizeActivity($data);

        /** @var TNCActivityStreamsEvent $object */
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $object->denormalize($activity);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($object)
    {
        return ($object instanceof TNCActivityStreamsEvent);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $className)
    {
        if (!class_exists($className)) {
            return false;
        }

        return is_subclass_of($className, TNCActivityStreamsEvent::class);
    }

    protected function normalizeActivity(Activity $activity)
    {
        return $this->getNonEmptyObjectVars($activity);
    }

    protected function getNonEmptyObjectVars($object)
    {
        $data = [];
        $vars = get_object_vars($object);
        foreach ($vars as $key => $value) {
            if (
              (is_string($value) && $value == '') ||
              (is_array($value) && count($value) === 0) ||
              is_null($value)
            ){
                continue;
            }

            switch (true) {

                case is_object($value):
                    $_data = $this->getNonEmptyObjectVars($value);
                    if (count($_data) > 0) {
                        $data[$key] = $_data;
                    }
                    break;

                case is_array($value):
                    $data[$key] = $value;
                    break;

                default:
                    $data[$key] = (string)$value;

            }
        }

        return $data;
    }

    protected function denormalizeActivity(array $data)
    {
        $activity = new Activity();

        foreach ($data as $key => $value) {
            switch (true) {
                case $key == 'actor':
                    if (is_array($value)) {
                        $activity->actor = new Actor($value['id'], isset($value['type']) ? $value['type'] : '');
                    }
                    break;
                case $key == 'object':
                    if (is_array($value)) {
                        $activity->object = new Obj($value['type'], isset($value['context']) ? $value['context'] : []);
                    }
                    break;
                case $key == 'context':
                    if (is_array($value)) {
                        $activity->context = $value;
                    }
                    break;
                default:
                    $activity->{$key} = $value;
            }
        }

        return $activity;
    }
}
