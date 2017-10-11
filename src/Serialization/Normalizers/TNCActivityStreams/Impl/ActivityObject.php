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

namespace TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl;

class ActivityObject
{
    /**
     * @var string
     */
    private $id = '';

    /**
     * @var string
     */
    private $objectType = '';

    /**
     * @var mixed
     */
    private $content = '';

    /**
     * @var ActivityObject[]
     */
    private $attachments = [];

    /**
     * @var mixed
     */
    private $summary = '';

    /**
     * @var string[]
     */
    private $downstreamDuplicates = [];

    /**
     * @var string[]
     */
    private $upstreamDuplicates = [];

    /**
     * @var ActivityObject
     */
    private $author = null;

    /**
     * @return array
     */
    public function getAll()
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return ActivityObject
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * @param string $objectType
     *
     * @return ActivityObject
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     *
     * @return ActivityObject
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject[] $attachments
     *
     * @return ActivityObject
     */
    public function setAttachments(array $attachments)
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject $attachment
     *
     * @return ActivityObject
     */
    public function addAttachment(ActivityObject $attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject
     */
    public function getAuthor()
    {
        return $this->author ?: ($this->author = new ActivityObject());
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject $author
     *
     * @return ActivityObject
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return \string[]
     */
    public function getDownstreamDuplicates()
    {
        return $this->downstreamDuplicates;
    }

    /**
     * @param \string[] $downstreamDuplicates
     *
     * @return ActivityObject
     */
    public function setDownstreamDuplicates($downstreamDuplicates)
    {
        $this->downstreamDuplicates = $downstreamDuplicates;

        return $this;
    }

    /**
     * @param string $downstreamDuplicate
     *
     * @return $this
     */
    public function addDownstreamDuplicate($downstreamDuplicate)
    {
        $this->downstreamDuplicates[] = $downstreamDuplicate;

        return $this;
    }

    /**
     * @return \string[]
     */
    public function getUpstreamDuplicates()
    {
        return $this->upstreamDuplicates;
    }

    /**
     * @param \string[] $upstreamDuplicates
     *
     * @return ActivityObject
     */
    public function setUpstreamDuplicates($upstreamDuplicates)
    {
        $this->upstreamDuplicates = $upstreamDuplicates;

        return $this;
    }

    /**
     * @param string $upstreamDuplicate
     *
     * @return $this
     */
    public function addUpstreamDuplicate($upstreamDuplicate)
    {
        $this->upstreamDuplicates[] = $upstreamDuplicate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     *
     * @return ActivityObject
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }
}