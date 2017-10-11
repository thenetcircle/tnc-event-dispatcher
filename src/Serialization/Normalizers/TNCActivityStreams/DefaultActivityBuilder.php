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

use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Actor;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Obj;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Provider;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Target;

class DefaultActivityBuilder implements ActivityBuilderInterface
{
    /**
     * @var Activity
     */
    protected $activity = null;

    /**
     * TNCActivityBuilder constructor.
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity|null $activity
     */
    public function __construct(Activity $activity = null)
    {
        $this->activity = null === $activity ? new Activity() : $activity;
        $this->activity->setPublished((new \DateTime())->format(\DateTime::RFC3339));
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param array $data
     *
     * @return DefaultActivityBuilder
     *
     * @throws InvalidArgumentException
     */
    public function setFromArray(array $data)
    {
        $supportedKeys = array_keys($this->activity->getAll());

        foreach ($data as $key => $value) {

            if (!in_array($key, $supportedKeys)) {
                throw new InvalidArgumentException(sprintf('Key %s is not supported.', $key));
            }

            $method = 'set' . ucfirst($key);
            if (in_array($key, ['actor', 'object', 'target', 'provider', 'generator'])) {
                $this->activity->{$method}($this->getActivityObjectFromData($value));
            }
            else {
                $this->activity->{$method}($value);
            }

        }

        return $this;
    }

    /**
     * @see \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->activity, $name], $arguments);
    }

    /**
     * @param mixed               $data
     * @param ActivityObject|null $prototype
     *
     * @return ActivityObject
     */
    protected function getActivityObjectFromData($data, $prototype = null)
    {
        $object = null === $prototype ? new ActivityObject() : $prototype;

        # Analyse $value
        switch (true)
        {
            case is_null($data):
                break;

            case is_string($data):
                $object->setId($data);
                break;

            case is_array($data):

                # If value only includes two elements and index is number, Will consider the first element is id,
                # second is objectType.
                if (count($data) === 1 && isset($data[0])) {
                    $object->setId($data[0]);
                }
                elseif (count($data) === 2 && isset($data[0])) {
                    $object->setObjectType($data[0]);
                    $object->setId($data[1]);
                }
                else {

                    $supportedKeys = array_keys($object->getAll());

                    foreach ($data as $key => $value) {

                        if (!in_array($key, $supportedKeys)) {
                            throw new InvalidArgumentException(
                              sprintf('ActivityObject key %s is not supported.', $key)
                            );
                        }

                        $method = 'set' . ucfirst($key);

                        if ($key == 'attachments') {

                            $attachments = array_map(
                              function($subdata){
                                return $this->getActivityObjectFromData($subdata);
                              },
                              (array) $value
                            );

                            $object->{$method}($attachments);

                        }
                        else {
                            $object->{$method}($value);
                        }

                    }

                }

                break;

            case is_object($data):
                $object = $data;
                break;
        }

        return $object;
    }
}
