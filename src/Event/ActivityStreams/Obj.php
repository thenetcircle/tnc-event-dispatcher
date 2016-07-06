<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Event\ActivityStreams;

use Tnc\Service\EventDispatcher\Normalizer;
use Tnc\Service\EventDispatcher\Serializer\Normalizable;

class Obj implements Normalizable
{
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
    private $displayName;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $published;

    /**
     * @var string
     */
    private $updated;

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
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer)
    {
        $vars = get_object_vars($this);
        $data = array();
        foreach ($vars as $_key => $_value) {
            if ($_value !== null) {
                if (is_array($_value)) {
                    $data[$_key] = $_value;
                } else {
                    $data[$_key] = (string)$_value;
                }
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Normalizer $normalizer)
    {
        foreach ($data as $_key => $_value) {
            $this->{$_key} = $_value;
        }
    }
}