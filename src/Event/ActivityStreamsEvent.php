<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Event;

use Tnc\Service\EventDispatcher\ActivityStreams\Obj;

abstract class ActivityEvent extends AbstractEvent implements Normalizable
{
    /**
     * @var Activity
     */
    protected $activity;

    /**
     * Create a new activity object
     *
     * @param string $id
     * @param string $objectType
     *
     * @return \Tnc\Service\EventDispatcher\ActivityStreams\Obj
     */
    public static function createObj($objectType = null, $id = null)
    {
        return (new Obj())->setObjectType($objectType)
                          ->setId($id);
    }


    public function __construct()
    {
        $this->published = (new \DateTime())->format(\DateTime::RFC3339);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        $actor = $this->activity->getActor();
        if($actor) {
            return $actor->getObjectType() . '_' . $actor->getId();
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getVerb()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getId()
    {
        $uuidArr = [$this->getProvider()->getId()];
        if($this->getActor()->getId() !== null) {
            $uuidArr[] = $this->getActor()->getObjectType();
            $uuidArr[] = $this->getActor()->getId();
        }
        $uuidArr[] = time();
        $uuidArr[] = sprintf('%03d', mt_rand(0, 999));
        return implode('-', $uuidArr);
    }

    /**
     * @return string
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    //Magic methods

    public function __call($name, $arguments)
    {
        if (0 === strpos($name, 'get') || 0 === strpos($name, 'set')) {
            // getters and setters
            $attributeName = lcfirst(substr($name, 3));
        }
    }
}