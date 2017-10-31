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

/**
 * Providers multiple ways to build \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity
 *
 * @method $this setVersion(string $version)
 * @method $this setId(string $id)
 * @method $this setVerb(string $verb)
 * @method $this setContent(mixed $content)
 * @method $this setActor(mixed $actor)
 * @method $this setObject(mixed $object)
 * @method $this setTarget(mixed $target)
 * @method $this setProvider(mixed $provider)
 * @method $this setGenerator(mixed $generator)
 * @method $this setPublished(string $published)
 * @method $this setTitle(string $title)
 */
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
            $this->activity->{$method}($value);
        }

        return $this;
    }

    /**
     * @see \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity
     *
     * @return $this
     */
    public function __call($name, $arguments)
    {
        call_user_func_array([$this->activity, $name], $arguments);

        return $this;
    }
}
