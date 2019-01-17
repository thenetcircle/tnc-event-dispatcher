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

namespace TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\Impl;

use TNC\EventDispatcher\Exception\InvalidArgumentException;

class ActivityObjectBuilder
{
    /**
     * @param mixed               $data
     * @param ActivityObject|null $prototype
     *
     * @return ActivityObject
     */
    public static function build($data, $prototype = null)
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

                # If data is array and includes numeric indexes,
                # Will consider:
                #   the first element is "id",
                #   second is            "objectType".
                #   third is             "content"
                #   forth is             "attachments"
                if (array_key_exists(0, $data)) {
                    switch (true) {
                        case count($data) >= 4:
                            $object->setAttachments($data[3]);
                        case count($data) >= 3:
                            $object->setContent($data[2]);
                        case count($data) >= 2:
                            $object->setObjectType($data[1]);
                        case count($data) >= 1:
                            $object->setId($data[0]);
                    }
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
                        $object->{$method}($value);
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