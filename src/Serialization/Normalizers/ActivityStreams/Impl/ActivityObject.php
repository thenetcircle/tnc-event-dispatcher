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
     * @var string
     */
    private $displayName = '';

    /**
     * @var string
     */
    private $image = '';

    /**
     * @var string
     */
    private $published = '';

    /**
     * @var string
     */
    private $updated = '';

    /**
     * @var string
     */
    private $url = '';

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
     * @return \TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\Impl\ActivityObject[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\Impl\ActivityObject[] $attachments
     *
     * @return ActivityObject
     */
    public function setAttachments(array $attachments)
    {
        array_walk($attachments, [$this, 'addAttachment']);

        return $this;
    }

    /**
     * @param mixed $attachment
     *
     * @return ActivityObject
     */
    public function addAttachment($attachment)
    {
        $this->attachments[] = ActivityObjectBuilder::build($attachment);

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\Impl\ActivityObject
     */
    public function getAuthor()
    {
        return $this->author ?: ($this->author = new ActivityObject());
    }

    /**
     * @param mixed $author
     *
     * @return ActivityObject
     */
    public function setAuthor($author)
    {
        $this->author = ActivityObjectBuilder::build($author);

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

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     *
     * @return ActivityObject
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return ActivityObject
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param string $published
     *
     * @return ActivityObject
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return string
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param string $updated
     *
     * @return ActivityObject
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return ActivityObject
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}