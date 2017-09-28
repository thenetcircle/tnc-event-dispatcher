<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Utils\ActivityStreams;

use TNC\EventDispatcher\Interfaces\Serializer;
use TNC\EventDispatcher\Serialization\Normalizer\Interfaces\Denormalizable;
use TNC\EventDispatcher\Serialization\Normalizer\Interfaces\Normalizable;

abstract class AbstractObject implements Normalizable, Denormalizable
{
    /**
     * @var Attachment[]
     */
    private $attachments = [];

    /**
     * @var Author
     */
    private $author = null;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string[]
     */
    private $downstreamDuplicates = [];

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $objectType;

    /**
     * @var string
     */
    private $published;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $updated;

    /**
     * @var string[]
     */
    private $upstreamDuplicates = [];

    /**
     * @var string
     */
    private $url;
    //---


    /**
     * AbstractObject constructor.
     */
    public function __construct($objectType = '', $id = '', $content = '')
    {
        $this->objectType = $objectType;
        $this->id = $id;
        $this->content = $content;
    }

    /**
     * @return \TNC\EventDispatcher\Utils\ActivityStreams\Attachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param \TNC\EventDispatcher\Utils\ActivityStreams\Attachment[] $attachments
     *
     * @return $this
     */
    public function setAttachments(array $attachments)
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @param \TNC\EventDispatcher\Utils\ActivityStreams\Attachment $attachment
     *
     * @return $this
     */
    public function addAttachment(Attachment $attachment)
    {
        array_push($this->attachments, $attachment);

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Utils\ActivityStreams\Author
     */
    public function getAuthor()
    {
        return $this->author ?: new Author();
    }
    
    /**
     * @param \TNC\EventDispatcher\Utils\ActivityStreams\Author $author
     *
     * @return $this
     */
    public function setAuthor(Author $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

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
     * @return $this
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return array
     */
    public function getDownstreamDuplicates()
    {
        return $this->downstreamDuplicates;
    }

    /**
     * @param array $downstreamDuplicates
     *
     * @return $this
     */
    public function setDownstreamDuplicates(array $downstreamDuplicates)
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
        array_push($this->downstreamDuplicates, $downstreamDuplicate);

        return $this;
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
     * @return $this
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
     * @return $this
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;

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
     * @return $this
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     *
     * @return $this
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

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
     * @return $this
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return array
     */
    public function getUpstreamDuplicates()
    {
        return $this->upstreamDuplicates;
    }

    /**
     * @param array $upstreamDuplicates
     *
     * @return $this
     */
    public function setUpstreamDuplicates(array $upstreamDuplicates)
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
        array_push($this->upstreamDuplicates, $upstreamDuplicate);

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
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(\TNC\EventDispatcher\Interfaces\Serializer $serializer)
    {
        $vars = get_object_vars($this);
        $data = [];

        foreach ($vars as $_key => $_value) {
            if (!empty($_value)) {
                if ($_key === 'attachments') {
                    $attachments = [];
                    foreach ($_value as $_attachment) {
                        array_push($attachments, $serializer->normalize($_attachment));
                    }
                    $data[$_key] = $attachments;
                }
                elseif (is_object($_value)) {
                    $data[$_key] = $serializer->normalize($_value);
                }
                elseif (is_array($_value)) {
                    $data[$_key] = $_value;
                }
                else {
                    $data[$_key] = (string)$_value;
                }
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(\TNC\EventDispatcher\Interfaces\Serializer $serializer, array $data)
    {
        foreach ($data as $_key => $_value) {
            if ($_key === 'attachments') {
                $attachments = [];
                foreach ($_value as $_attachment) {
                    array_push($attachments, $serializer->denormalize($_attachment, Attachment::class));
                }
                $this->attachments = $attachments;
            }
            elseif ($_key === 'author') {
                $this->author = $serializer->denormalize($_value, Author::class);
            }
            else {
                $this->{$_key} = $_value;
            }
        }
    }
}